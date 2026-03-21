<?php

namespace Tests\Feature;

use Firebase\JWT\JWT;
use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    private function createToken($expired = false)
    {
        $secret = config('services.jwt.secret');
        $payload = [
            'sub' => '123',
            'iat' => time() - ($expired ? 7200 : 0),
            'exp' => time() + ($expired ? -3600 : 3600),
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function test_orders_route_requires_token()
    {
        $response = $this->getJson($this->api('/orders'));
        $response->assertStatus(401);
    }

    public function test_orders_route_accepts_valid_token()
    {
        $token = $this->createToken();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->api('/orders'));

        // Should at least pass middleware and be proxied (e.g. 200 or 503)
        $this->assertNotEquals(401, $response->status());
    }

    public function test_orders_route_rejects_expired_token()
    {
        $token = $this->createToken(true);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->api('/orders'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token expired']);
    }

    public function test_orders_route_rejects_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson($this->api('/orders'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid token']);
    }

    public function test_products_route_requires_token()
    {
        $response = $this->getJson($this->api('/products'));
        $response->assertStatus(401);
    }

    public function test_products_route_accepts_valid_token()
    {
        $token = $this->createToken();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->api('/products'));

        // Should at least pass middleware and be proxied (e.g. 200 or 503)
        $this->assertNotEquals(401, $response->status());
    }

    public function test_products_route_rejects_expired_token()
    {
        $token = $this->createToken(true);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson($this->api('/products'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token expired']);
    }

    public function test_products_route_rejects_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson($this->api('/products'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid token']);
    }
}
