<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class CorrelationId
    {
        public function handle($request, Closure $next)
        {
            $id = $request->header('X-Correlation-Id') ?? Str::uuid()->toString();

            $request->headers->set('X-Correlation-Id', $id);

            Log::withContext([
                'correlation_id' => $id
            ]);

            $response = $next($request);

            $response->headers->set('X-Correlation-Id', $id);

            return $response;
        }
    }
