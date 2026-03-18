<?php

namespace App\Http\Middleware;

use App\Support\InternalRequestSigner;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

final class VerifyInternalSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodIdempotent() || app()->environment('testing')) {
            return $next($request);
        }

        $userId = (string) $request->header('X-User-Id', '');
        $correlationId = (string) $request->header('X-Correlation-ID', '');
        $timestamp = (string) $request->header('X-Timestamp', '');
        $signature = (string) $request->header('X-Internal-Signature', '');
        $nonce = (string) $request->header('X-Nonce', '');
        logger()->info('VerifyInternalSignature', ['X-Nonce' => $nonce]);

        if (
            $userId === '' ||
            $correlationId === '' ||
            $timestamp === '' ||
            $signature === '' ||
            $nonce === ''
        ) {
            abort(401, 'Missing internal signature headers.');
        }

        if (! ctype_digit($timestamp)) {
            abort(401, 'Invalid timestamp.');
        }

        // max drift 5 minut
        if (abs(time() - (int) $timestamp) > 300) {
            abort(401, 'Expired internal signature.');
        }

        $secret = config('services.internal.token');

        if (! is_string($secret) || $secret === '') {
            throw new \RuntimeException('Internal token is not configured');
        }

        // normalizace path kvůli /orders vs /orders/
        $path = rtrim('/'.$request->path(), '/');

        $expectedSignature = InternalRequestSigner::sign(
            method: $request->method(),
            path: $path,
            userId: $userId,
            correlationId: $correlationId,
            nonce: $nonce,
            timestamp: $timestamp,
            secret: $secret
        );

        if (! hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid internal signature.');
        }

        // replay protection (atomicky)
        $cacheKey = 'nonce:'.$nonce;

        $ok = Cache::add($cacheKey, 1, now()->addSeconds(300));

        if (! $ok) {
            abort(409, 'Duplicate request detected.');
        }

        return $next($request);
    }
}
