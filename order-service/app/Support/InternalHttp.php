<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

final class InternalHttp
{
    public static function request(
        string $method,
        string $baseUrl,
        string $path,
        array $query = [],
        array $body = []
    ): PendingRequest {

        $timestamp = (string) now()->timestamp;

        $signature = InternalRequestSigner::sign(
            method: $method,
            path: $path,
            userId: (string) RequestContext::userId(),
            correlationId: (string) RequestContext::correlationId(),
            timestamp: $timestamp,
            secret: config('services.internal.token')
        );

        $headers = [
            'X-User-Id' => RequestContext::userId(),
            'X-Correlation-ID' => RequestContext::correlationId(),
            'X-Service-Name' => config('app.name'),
            'X-Timestamp' => $timestamp,
            'X-Internal-Signature' => $signature,
        ];

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->timeout(2)
            ->connectTimeout(1)
            ->retry(2, 150, fn (Throwable $e) => $e instanceof ConnectionException)
            ->withHeaders($headers)
            ->when($body !== [], fn ($http) => $http->asJson());
    }

    public static function get(string $baseUrl, string $path, array $query = [])
    {
        return self::request('GET', $baseUrl, $path, $query)
            ->get($path, $query);
    }

    public static function post(string $baseUrl, string $path, array $body = [])
    {
        return self::request('POST', $baseUrl, $path, [], $body)
            ->post($path, $body);
    }
}
