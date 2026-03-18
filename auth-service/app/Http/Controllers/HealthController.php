<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthController extends Controller
{
    public function health()
    {
        return response()->json([
            'status' => 'UP',
            'service' => config('app.name'),
            'version' => config('app.version'),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function ready()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
        ];

        $status = collect($checks)->every(fn ($v) => $v === 'UP') ? 'UP' : 'DOWN';

        return response()->json([
            'status' => $status,
            'service' => config('app.name'),
            'version' => config('app.version'),
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $status === 'UP' ? 200 : 503);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'UP';
        } catch (Throwable) {
            return 'DOWN';
        }
    }

    private function checkRedis(): string
    {
        try {
            Redis::ping();

            return 'UP';
        } catch (Throwable) {
            return 'DOWN';
        }
    }
}
