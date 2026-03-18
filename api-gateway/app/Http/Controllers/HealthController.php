<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class HealthController extends Controller
{
    public function index()
    {
        $services = config('services.proxy.services');

        $results = [];

        foreach ($services as $name => $service) {

            $start = microtime(true);

            try {
                $response = Http::timeout(2)->get(rtrim($service['url'], '/').'/health');

                $latency = round((microtime(true) - $start) * 1000);

                $results[$name] = [
                    'status' => $response->successful() ? 'UP' : 'DOWN',
                    'http_status' => $response->status(),
                    'latency_ms' => $latency,
                ];
            } catch (\Throwable $e) {
                $latency = round((microtime(true) - $start) * 1000);

                $results[$name] = [
                    'status' => 'DOWN',
                    'latency_ms' => $latency,
                    'error' => class_basename($e),
                ];

            }

        }

        return response()->json([
            'gateway' => [
                'status' => 'UP',
                'version' => config('app.version'),
                'service' => config('app.name'),
            ],
            'timestamp' => now()->toISOString(),
            'services' => $results,
        ]);
    }
}
