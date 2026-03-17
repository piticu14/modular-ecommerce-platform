<?php

namespace Tests\Feature;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_register_proxies_to_auth_service()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test' . rand() . '@example.com',
            'password' => 'password',
        ]);

        // We expect either success or a specific failure if auth service is down,
        // but it should at least be routed correctly.
        $this->assertContains($response->status(), [201, 422, 503]);
    }

    public function test_protected_route_requires_token()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Missing token']);
    }

    public function test_protected_route_accepts_valid_token()
    {
        $secret = config('services.jwt.secret');
        $payload = [
            'sub' => '123',
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $token = JWT::encode($payload, $secret, 'HS256');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/products');

        // Should at least pass middleware and be proxied (e.g. 200 or 503)
        $this->assertNotEquals(401, $response->status());
    }

    public function test_protected_route_rejects_expired_token()
    {
        $secret = config('services.jwt.secret');
        $payload = [
            'sub' => '123',
            'iat' => time() - 7200,
            'exp' => time() - 3600
        ];
        $token = JWT::encode($payload, $secret, 'HS256');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/products');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token expired']);
    }

    public function test_protected_route_rejects_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/products');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid token']);
    }
}
