<?php

namespace App\Services\Proxy;

use App\Support\InternalRequestSigner;
use App\Support\RequestContext;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceProxy
{
    public function forward(Request $request, string $service): Response
    {
        $config = $this->getServiceConfig($service);
        $url = $this->resolveUrl($request, $config);
        $idempotencyKey = $request->header('Idempotency-Key') ?: (string) Str::uuid();

        $attempts = $config['retries'] + 1;

        return retry(
            $attempts,
            function () use ($request, $config, $url, $idempotencyKey, $service) {
                $headers = array_filter([
                    'Authorization' => $request->header('Authorization'),
                    'X-User-Id' => $request->header('X-User-Id'),
                    'X-Service-Name' => config('app.name'),
                    'X-Correlation-ID' => $request->header('X-Correlation-ID'),
                    'Accept' => $request->header('Accept', 'application/json'),
                    'Content-Type' => $request->header('Content-Type', 'application/json'),
                    'Idempotency-Key' => $idempotencyKey,
                ]);

                if ($config['signed']) {
                    $headers = $this->signHeaders($request, $headers);
                }

                /** @var Response $response */
                $response = Http::timeout($config['timeout'])
                    ->withHeaders($headers)
                    ->send(
                        $request->method(),
                        $url,
                        [
                            'query' => $request->query(),
                            'json' => $request->all(),
                        ]
                    );

                if ($response->failed()) {

                    Log::error('API Gateway downstream error', [
                        'service' => $service,
                        'url' => $url,
                        'method' => $request->method(),
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'correlation_id' => $headers['X-Correlation-ID'] ?? null,
                    ]);
                }

                if ($response->serverError()) {
                    throw new \Exception('Retryable error');
                }

                return $response;
            },
            $config['retry_sleep']
        );
    }

    private function resolveUrl(Request $request, array $config): string
    {
        $baseUrl = $config['url'];
        $path = ltrim($request->path(), '/');

        if (str_starts_with($path, 'api/')) {
            $path = substr($path, 4);
        }

        $path = $this->mapVersion($path, $config);

        return rtrim($baseUrl, '/').'/api/'.ltrim($path, '/');
    }

    private function mapVersion(string $path, array $config): string
    {
        $segments = explode('/', $path);

        if (empty($segments[0])) {
            return $path;
        }

        $gatewayVersion = $segments[0];

        if (! preg_match('/^v\d+$/', $gatewayVersion)) {
            return $path;
        }

        $versionMap = $config['version_map'] ?? [];
        $serviceVersion = $versionMap[$gatewayVersion] ?? $gatewayVersion;
        $segments[0] = $serviceVersion;

        return implode('/', $segments);
    }

    /**
     * @return array{url: string, signed:bool, timeout: int, retries: int, retry_sleep: int, version_map: array<string, string>}
     */
    private function getServiceConfig(string $service): array
    {
        $config = config("services.proxy.services.$service");

        if (! is_array($config)) {
            abort(404, "Service [$service] is not configured.");
        }

        /** @var array{url: string, signed:bool, timeout: int, retries: int, retry_sleep:int, version_map: array<string, string>} $config */
        return $config;
    }

    private function signHeaders(Request $request, array $headers): array
    {
        $timestamp = (string) now()->timestamp;
        $nonce = (string) Str::uuid();
        $secret = config('services.internal.token');

        if (! is_string($secret) || $secret === '') {
            throw new \RuntimeException('Internal token is not configured');
        }

        $signature = InternalRequestSigner::sign(
            method: $request->method(),
            path: '/'.$request->path(),
            userId: (string) RequestContext::userId(),
            correlationId: (string) RequestContext::correlationId(),
            nonce: $nonce,
            timestamp: $timestamp,
            secret: $secret
        );

        $headers['X-Nonce'] = $nonce;
        $headers['X-Timestamp'] = $timestamp;
        $headers['X-Internal-Signature'] = $signature;

        return $headers;
    }
}
