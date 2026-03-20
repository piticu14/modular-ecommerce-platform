<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function api(string $path): string
    {
        return '/api/' . config('api.prefix') . $path;
    }

    protected function fakeService(string $service, string $path, array $response, int $status = 200): void
    {
        $gatewayVersion = config('api.prefix');

        $serviceConfig = config("services.proxy.services.$service");

        $serviceVersion = $serviceConfig['version_map'][$gatewayVersion] ?? $gatewayVersion;

        $baseUrl = $serviceConfig['url'];


        Http::fake([
            sprintf(
                '%s/api/%s%s',
                $baseUrl,
                $serviceVersion,
                $path
            ) => Http::response($response, $status),
        ]);
    }
}
