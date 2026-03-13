<?php

    namespace App\Http\Controllers;

    use Illuminate\Support\Facades\Http;

    class HealthController extends Controller
    {
        public function index()
        {
            $services = config('services.proxy.services');

            $results = [];

            foreach ($services as $name => $url) {

                try {

                    $response = Http::timeout(2)->get("$url/health");

                    $results[$name] = [
                        'status' => $response->successful() ? 'UP' : 'DOWN'
                    ];

                } catch (\Throwable $e) {

                    $results[$name] = [
                        'status' => 'DOWN'
                    ];

                }

            }

            return response()->json([
                'gateway' => 'UP',
                'services' => $results
            ]);
        }
    }
