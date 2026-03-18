<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if (!is_string($key) || $key === '') {
            return $next($request);
        }

        $cacheKey = "orders:idempotency:{$key}";

        $lock = Cache::lock($cacheKey.':lock', 10);

        try {
            $lock->block(5);

            /** @var array<string, mixed>|null $existing */
            $existing = Cache::get($cacheKey);

            if ($existing !== null) {
                return response()->json([
                    'data' => $existing,
                ]);
            }

            $response = $next($request);

            if ($response->isSuccessful()) {
                $data = $response->getData(true);

                if (is_array($data) && isset($data['data'])) {
                    Cache::put(
                        $cacheKey,
                        $data['data'],
                        now()->addDay()
                    );
                }
            }

            return $response;
        } finally {
            $lock->release();
        }
    }
}
