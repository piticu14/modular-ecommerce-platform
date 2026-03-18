<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyJwt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProxyTest extends TestCase
{
    public function test_proxies_auth_login()
    {
        Http::fake([
            'http://auth-nginx/api/auth/login' => Http::response(['access_token' => 'fake-token'], 200),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['access_token' => 'fake-token']);
    }

    public function test_proxies_get_products()
    {
        Http::fake([
            'http://product-nginx/api/products' => Http::response(['data' => []], 200),
        ]);

        // Need valid token to pass VerifyJwt middleware
        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_proxies_post_orders()
    {
        Http::fake([
            'http://order-nginx/api/orders' => Http::response(['id' => 1], 201),
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson('/api/orders', [
            'product_id' => 'some-uuid',
            'quantity' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJson(['id' => 1]);
    }
}
