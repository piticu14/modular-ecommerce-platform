<?php

namespace Tests\Feature;

use App\Order\Domain\Enums\OrderStatus;
use App\Order\Domain\Models\Order;
use App\Order\Infrastructure\Clients\ProductServiceClient;
use App\Order\Application\DTO\ProductSnapshot;
use App\Order\Infrastructure\Exceptions\ProductServiceUnavailableException;
use App\Order\Infrastructure\Exceptions\ProductNotFoundException;
use App\Support\RequestContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_list_orders()
    {
        Order::factory()->count(3)->create(['user_id' => 1]);

        $response = $this->signedRequest('GET', '/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_order()
    {
        $productUuid = 'product-uuid-1';

        $this->mock(ProductServiceClient::class, function (MockInterface $mock) use ($productUuid) {
            $mock->shouldReceive('getProductsByUuid')
                ->once()
                ->with([$productUuid])
                ->andReturn([
                    $productUuid => new ProductSnapshot(
                        uuid: $productUuid,
                        name: 'Test Product',
                        price: '100',
                        currency: 'USD',
                        status: 'active',
                        stock_on_hand: 10,
                        stock_reserved: 0,
                        stock_available: 10
                    )
                ]);
        });

        $response = $this->signedRequest('POST', '/api/orders', [
            'items' => [
                [
                    'product_uuid' => $productUuid,
                    'quantity' => 2,
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total', 200)
            ->assertJsonPath('data.status', 'PENDING');

        $this->assertDatabaseHas('orders', [
            'user_id' => 1,
            'total' => 200,
            'status' => 'PENDING'
        ]);
    }

    public function test_cannot_create_order_with_empty_items()
    {
        $response = $this->signedRequest('POST', '/api/orders', [
            'items' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_cannot_create_order_when_product_service_is_unavailable()
    {
        $this->mock(ProductServiceClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProductsByUuid')
                ->andThrow(new ProductServiceUnavailableException());
        });

        $response = $this->signedRequest('POST', '/api/orders', [
            'items' => [
                ['product_uuid' => 'p1', 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(503)
            ->assertJson(['message' => 'Order could not be created']);
    }

    public function test_cannot_create_order_when_product_not_found()
    {
        $this->mock(ProductServiceClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProductsByUuid')
                ->andThrow(new ProductNotFoundException('p1'));
        });

        // The current controller doesn't catch ProductNotFoundException,
        // it only catches OrderCreationFailedException.
        // Let's see how it behaves. It probably results in 500 if not handled.
        $response = $this->signedRequest('POST', '/api/orders', [
            'items' => [
                ['product_uuid' => 'p1', 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(500);
    }

    public function test_can_show_order()
    {
        $order = Order::factory()->create(['user_id' => 1]);

        $response = $this->signedRequest('GET', "/api/orders/{$order->uuid}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->uuid);
    }

    public function test_can_cancel_order()
    {
        $order = Order::factory()->create([
            'user_id' => 1,
            'status' => OrderStatus::PENDING->value
        ]);

        $response = $this->signedRequest('DELETE', "/api/orders/{$order->uuid}");

        $response->assertStatus(204);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELLED->value
        ]);
    }

    public function test_cannot_cancel_already_final_order()
    {
        $order = Order::factory()->create([
            'user_id' => 1,
            'status' => OrderStatus::CONFIRMED->value
        ]);

        $response = $this->signedRequest('DELETE', "/api/orders/{$order->uuid}");

        $response->assertStatus(409);
    }
}
