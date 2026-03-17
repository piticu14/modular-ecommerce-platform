<?php

namespace Tests\Feature;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    private function createToken($expired = false)
    {
        $secret = config('services.jwt.secret');
        $payload = [
            'sub' => '123',
            'iat' => time() - ($expired ? 7200 : 0),
            'exp' => time() + ($expired ? -3600 : 3600)
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }

    public function test_orders_route_requires_token()
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }

    public function test_orders_route_accepts_valid_token()
    {
        Http::fake([
            'http://order-nginx/api/orders*' => Http::response([], 200)
        ]);

        $token = $this->createToken();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/orders');

        $response->assertStatus(200);
    }

    public function test_products_route_requires_token()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    public function test_products_route_accepts_valid_token()
    {
        Http::fake([
            'http://product-nginx/api/products*' => Http::response([], 200)
        ]);

        $token = $this->createToken();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/products');

        $response->assertStatus(200);
    }
}
