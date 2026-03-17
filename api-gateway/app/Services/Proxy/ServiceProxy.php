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
            $url = rtrim($baseUrl, '/') . '/' . ltrim($request->path(), '/');

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

            return Http::timeout(5)
                ->retry(2, 100)
                ->withHeaders($headers)
                ->send(
                    $request->method(),
                    $url,
                    $request->method() === 'GET' ? [
                        'query' => $request->query(),
                    ] : [
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
