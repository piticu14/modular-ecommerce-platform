<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyJwt;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_proxies_get_orders()
    {
        $this->fakeService('orders', '/orders', [
            'data' => [],
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api('/orders'));

        $response->assertOk()
            ->assertJson(['data' => []]);
    }

    public function test_proxies_get_order_detail()
    {
        $orderUuid = (string) Str::uuid();

        $this->fakeService('orders', "/orders/{$orderUuid}", [
            'id' => $orderUuid,
        ]);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->getJson($this->api("/orders/{$orderUuid}"));

        $response->assertOk()
            ->assertJson(['id' => $orderUuid]);
    }

    public function test_proxies_create_order()
    {
        $orderUuid = (string) Str::uuid();
        $productUuid = (string) Str::uuid();

        $this->fakeService('orders', '/orders', [
            'id' => $orderUuid,
        ], 201);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->postJson($this->api('/orders'), [
            'items' => [
                [
                    'product_uuid' => $productUuid,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson(['id' => $orderUuid]);
    }

    public function test_proxies_delete_order()
    {
        $orderId = 1;

        $this->fakeService('orders', "/orders/{$orderId}", [], 204);

        $this->withoutMiddleware(VerifyJwt::class);

        $response = $this->deleteJson($this->api("/orders/{$orderId}"));

        $response->assertStatus(204);
    }
}
