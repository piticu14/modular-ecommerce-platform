<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');

        if (! $key) {
            return $next($request);
        }

        $redisKey = "orders:idempotency:$key";

        /** @var string|null $existing */
        $existing = Redis::get($redisKey);

        if ($existing) {

            return response()->json([
                'data' => json_decode($existing, true),
            ]);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 201) {

            $data = $response->getData(true);

            Redis::setex(
                $redisKey,
                86400,
                json_encode($data['data'])
            );
        }

        return $response;
    }
}
