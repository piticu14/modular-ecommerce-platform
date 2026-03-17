<?php


    namespace App\Services\Proxy;

    use App\Support\InternalRequestSigner;
    use App\Support\RequestContext;
    use Illuminate\Http\Client\Response;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;

    class ServiceProxy
    {
        public function forward(Request $request, string $baseUrl, bool $signed = false): Response
        {
            $path = ltrim($request->path(), '/');
            if (str_starts_with($path, 'api/')) {
                $path = substr($path, 4);
            }
            $url = rtrim($baseUrl, '/') . '/api/' . ltrim($path, '/');

            // Log for debugging (only in testing)
            if (app()->environment('testing')) {
                \Log::info("Forwarding to: $url");
            }



            $headers = $this->forwardHeaders($request);

            if ($signed) {
                $timestamp = (string)now()->timestamp;

                $signature = InternalRequestSigner::sign(
                    method: $request->method(),
                    path: '/' . $request->path(),
                    userId: (string) RequestContext::userId(),
                    correlationId: (string) RequestContext::correlationId(),
                    timestamp: $timestamp,
                    secret: config('services.internal.token')
                );

                $headers['X-Timestamp'] = $timestamp;
                $headers['X-Internal-Signature'] = $signature;
            }

            return Http::timeout(10)
                ->retry(3, 500)
                ->withHeaders($headers)
                ->send(
                    $request->method(),
                    $url,
                    [
                        'query' => $request->query(),
                        'json' => $request->all(),
                    ]
                );
        }

        private function forwardHeaders(Request $request): array
        {
            return array_filter([
                'Authorization' => $request->header('Authorization'),
                'X-User-Id' => $request->header('X-User-Id'),
                'X-Service-Name' => config('app.name'),
                'X-Correlation-ID' => $request->header('X-Correlation-ID'),
                'Accept' => $request->header('Accept', 'application/json'),
                'Content-Type' => $request->header('Content-Type', 'application/json'),

            ]);
        }
    }
