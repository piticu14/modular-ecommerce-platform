<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function signedRequest(string $method, string $uri, array $data = [], int $userId = 1): \Illuminate\Testing\TestResponse
    {
        $timestamp = (string) now()->timestamp;
        $correlationId = 'test-correlation-id';
        $path = '/' . ltrim($uri, '/');

        $signature = \App\Support\InternalRequestSigner::sign(
            method: $method,
            path: $path,
            userId: (string) $userId,
            correlationId: $correlationId,
            timestamp: $timestamp,
            secret: config('services.internal.token')
        );

        $headers = [
            'X-User-Id' => (string) $userId,
            'X-Correlation-ID' => $correlationId,
            'X-Timestamp' => $timestamp,
            'X-Internal-Signature' => $signature,
        ];

        return $this->json($method, $uri, $data, $headers);
    }
}
