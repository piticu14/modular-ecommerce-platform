<?php

    namespace App\Http\Middleware;

    use App\Support\InternalRequestSigner;
    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redis;
    use Symfony\Component\HttpFoundation\Response;

    final class VerifyInternalSignature
    {
        public function handle(Request $request, Closure $next): Response
        {
            if (app()->environment('testing')) {
                return $next($request);
            }

            $userId = (string) $request->header('X-User-Id', '');
            $correlationId = (string) $request->header('X-Correlation-ID', '');
            $timestamp = (string) $request->header('X-Timestamp', '');
            $signature = (string) $request->header('X-Internal-Signature', '');

            if ($userId === '' || $correlationId === '' || $timestamp === '' || $signature === '') {
                abort(401, 'Missing internal signature headers.');
            }

            if (!ctype_digit($timestamp)) {
                abort(401, 'Invalid timestamp.');
            }

            // max drift 5 minut
            if (abs(time() - (int) $timestamp) > 300) {
                abort(401, 'Expired internal signature.');
            }

            // normalizace path kvůli /orders vs /orders/
            $path = rtrim('/' . $request->path(), '/');

            $expectedSignature = InternalRequestSigner::sign(
                method: $request->method(),
                path: $path,
                userId: $userId,
                correlationId: $correlationId,
                timestamp: $timestamp,
                secret: config('services.internal.token')
            );

            if (!hash_equals($expectedSignature, $signature)) {
                abort(403, 'Invalid internal signature.');
            }

            // replay protection (atomicky)
            $nonce = 'sig:' . hash('sha256', $signature . $timestamp);

            $ok = Redis::set($nonce, 1, 'NX', 'EX', 300);

            if (!$ok) {
                abort(401, 'Replay attack detected.');
            }

            return $next($request);
        }
    }
