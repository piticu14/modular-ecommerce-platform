<?php

namespace App\Services\Proxy;

use App\Support\InternalRequestSigner;
use App\Support\RequestContext;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServiceProxy
{
    public function forward(Request $request, string $service): Response
    {
        $config = $this->getServiceConfig($service);
        $url = $this->resolveUrl($request, $config);
        $headers = $this->forwardHeaders($request);

        if ($config['signed'] ?? false) {
            $headers = $this->signHeaders($request, $headers);
        }

        return Http::timeout($config['timeout'] ?? 10)
            ->retry($config['retries'] ?? 3, $config['retry_sleep'] ?? 500)
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

    private function getServiceConfig(string $service): array
    {
        $config = config("services.proxy.services.$service");

        if (! $config) {
            abort(404, "Service [$service] is not configured.");
        }

        return $config;
    }

    private function signHeaders(Request $request, array $headers): array
    {
        $timestamp = (string) now()->timestamp;

        $signature = InternalRequestSigner::sign(
            method: $request->method(),
            path: '/'.$request->path(),
            userId: (string) RequestContext::userId(),
            correlationId: (string) RequestContext::correlationId(),
            timestamp: $timestamp,
            secret: config('services.internal.token')
        );

        $headers['X-Timestamp'] = $timestamp;
        $headers['X-Internal-Signature'] = $signature;

        return $headers;
    }
}
