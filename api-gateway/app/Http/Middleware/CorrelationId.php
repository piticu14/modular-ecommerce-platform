<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class CorrelationId
    {
        public function handle(Request $request, Closure $next)
        {
            $id = $request->header('X-Correlation-ID') ?? (string) Str::uuid();

            $request->headers->set('X-Correlation-ID', $id);

            Log::withContext([
                'correlation_id' => $id
            ]);

            $response = $next($request);

            $response->headers->set('X-Correlation-ID', $id);

            return $response;
        }
    }
