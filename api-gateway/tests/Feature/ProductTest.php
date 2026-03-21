<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyJwt;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_proxies_get_products()
    {
        $this->fakeService('products', '/products', [
            'data' => [],
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api('/products'));

        $response->assertOk()
            ->assertJson(['data' => []]);
    }

    public function test_proxies_get_product_detail()
    {
        $productUuid = (string) Str::uuid();

        $this->fakeService('products', "/products/{$productUuid}", [
            'id' => $productUuid,
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api("/products/{$productUuid}"));

        $response->assertOk()
            ->assertJson(['id' => $productUuid]);
    }

    public function test_proxies_create_product()
    {
        $productUuid = (string) Str::uuid();
        $this->fakeService('products', '/products', [
            'id' => $productUuid,
        ], 201);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/products'), [
            'name' => 'Test product',
            'price' => 100000,
            'currency' => 'CZK',
            'stock_on_hand' => 100,
        ]);

        $response->assertStatus(201)
            ->assertJson(['id' => $productUuid]);
    }

    public function test_proxies_delete_product()
    {
        $productUuid = (string) Str::uuid();

        $this->fakeService('products', "/products/{$productUuid}", [], 204);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->deleteJson($this->api("/products/{$productUuid}"));

        $response->assertStatus(204);
    }

    public function test_proxies_get_product_stock_reservations()
    {
        $productUuid = (string) Str::uuid();

        $this->fakeService('products', "/products/{$productUuid}/stock-reservations", [
            'data' => [],
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api("/products/{$productUuid}/stock-reservations"));

        $response->assertOk()
            ->assertJson(['data' => []]);
    }
}
