<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyJwt;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_proxies_register_user()
    {
        $this->fakeService('auth', '/auth/register', [
            'data' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ], 201);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/auth/register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'John Doe')
            ->assertJsonPath('data.email', 'john@example.com');
    }

    public function test_proxies_login_user()
    {
        $this->fakeService('auth', '/auth/login', [
            'access_token' => 'token',
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/auth/login'), [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson(['access_token' => 'token']);
    }

    public function test_proxies_get_me()
    {
        $this->fakeService('auth', '/auth/me', [
            'id' => 1,
            'email' => 'john@example.com',
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api('/auth/me'));

        $response->assertOk()
            ->assertJson(['email' => 'john@example.com']);
    }

    public function test_proxies_refresh_token()
    {
        $this->fakeService('auth', '/auth/refresh', [
            'access_token' => 'new-token',
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/auth/refresh'));

        $response->assertOk()
            ->assertJson(['access_token' => 'new-token']);
    }

    public function test_proxies_logout()
    {
        $this->fakeService('auth', '/auth/logout', [
            'message' => 'Logged out',
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/auth/logout'));

        $response->assertOk()
            ->assertJson(['message' => 'Logged out']);
    }
}
