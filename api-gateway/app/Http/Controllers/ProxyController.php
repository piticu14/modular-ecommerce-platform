<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Symfony\Component\HttpFoundation\Response;

    class ProxyController extends Controller
    {
        public function forward(Request $request, string $service, string $path = '')
        {
            $services = config('services.proxy.services');

            $baseUrl = $services[$service] ?? null;

            if (!$baseUrl) {
                abort(Response::HTTP_NOT_FOUND, 'Unknown service.');
            }

            $url = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');

            $response = Http::timeout(5)
                ->retry(2, 100)
                ->withHeaders($this->forwardHeaders($request))
                ->send(
                    $request->method(),
                    $url,
                    [
                        'query' => $request->query(),
                        'json' => $request->all(),
                    ]
                );

            return response($response->body(), $response->status())
                ->withHeaders($this->responseHeaders($response->headers()));
        }

        private function forwardHeaders(Request $request): array
        {
            return array_filter([
                'Authorization' => $request->header('Authorization'),
                'X-User-Id' => $request->header('X-User-Id'),
                'X-Correlation-Id' => $request->header('X-Correlation-Id'),
                'Accept' => $request->header('Accept', 'application/json'),
                'Content-Type' => $request->header('Content-Type'),
            ]);
        }

        private function responseHeaders(array $headers): array
        {
            $allowed = [
                'content-type',
                'cache-control',
            ];

            $result = [];

            foreach ($headers as $name => $values) {
                if (!in_array(strtolower($name), $allowed, true)) {
                    continue;
                }

                $result[$name] = is_array($values)
                    ? implode(', ', $values)
                    : $values;
            }

            return $result;
        }
    }
