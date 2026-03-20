<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

final class InternalHttp
{
    private static function request(
        string $method,
        string $baseUrl,
        string $path,

    ): PendingRequest {

        $timestamp = (string) now()->timestamp;
        $secret = config('services.internal.token');
        $nonce = (string) Str::uuid();

        if (! is_string($secret) || $secret === '') {
            throw new \RuntimeException('Internal token is not configured');
        }

        $signature = InternalRequestSigner::sign(
            method: $method,
            path: $path,
            userId: (string) RequestContext::userId(),
            correlationId: (string) RequestContext::correlationId(),
            nonce: $nonce,
            timestamp: $timestamp,
            secret: $secret
        );

        $headers = [
            'X-User-Id' => RequestContext::userId(),
            'X-Correlation-ID' => RequestContext::correlationId(),
            'X-Service-Name' => config('app.name'),
            'X-Timestamp' => $timestamp,
            'X-Internal-Signature' => $signature,
            'X-Nonce' => $nonce,
        ];

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->timeout(2)
            ->connectTimeout(1)
            ->retry(2, 150, fn (Throwable $e) => $e instanceof ConnectionException)
            ->withHeaders($headers);
    }

    public static function get(string $baseUrl, string $path, array $query = [])
    {
        return self::request('GET', $baseUrl, $path)
            ->get($path, $query);
    }

    public static function post(string $baseUrl, string $path, array $body = [])
    {
        return self::request('POST', $baseUrl, $path)
            ->asJson()
            ->post($path, $body);
    }
}
