<?php

namespace Tests;

use App\Support\InternalRequestSigner;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function signedRequest(string $method, string $uri, array $data = [], int $userId = 1): TestResponse
    {
        $timestamp = (string) now()->timestamp;
        $correlationId = 'test-correlation-id';
        $path = '/'.ltrim($uri, '/');

        $signature = InternalRequestSigner::sign(
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
