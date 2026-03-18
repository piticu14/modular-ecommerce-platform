<?php

namespace Tests\Feature;

use App\Messaging\Consumers\OrderCreatedHandler;
use App\Messaging\Infrastructure\Models\OutboxEvent;
use App\Messaging\Infrastructure\Models\ProcessedEvent;
use App\Product\Domain\Models\Product;
use App\Stock\Domain\Models\StockReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StockReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_created_event_reserves_stock()
    {
        $product = Product::factory()->create([
            'stock_on_hand' => 10,
            'stock_reserved' => 0,
        ]);

        $orderUuid = (string) Str::uuid();
        $orderItemUuid = (string) Str::uuid();
        $eventId = (string) Str::uuid();

        $event = [
            'event_id' => $eventId,
            'correlation_id' => 'test-correlation-id',
            'data' => [
                'order_uuid' => $orderUuid,
                'items' => [
                    [
                        'product_uuid' => $product->uuid,
                        'order_item_uuid' => $orderItemUuid,
                        'quantity' => 3,
                    ],
                ],
            ],
        ];

        /** @var OrderCreatedHandler $handler */
        $handler = app(OrderCreatedHandler::class);
        $handler->handle($event);

        // Verify Product stock updated
        $product->refresh();
        $this->assertEquals(3, $product->stock_reserved);
        $this->assertEquals(7, $product->stock_available);

        // Verify StockReservation created
        $this->assertDatabaseHas('stock_reservations', [
            'order_uuid' => $orderUuid,
            'order_item_uuid' => $orderItemUuid,
            'product_id' => $product->id,
            'quantity' => 3,
            'status' => 'reserved',
        ]);

        // Verify ProcessedEvent created (idempotency)
        $this->assertDatabaseHas('processed_events', [
            'event_id' => $eventId,
            'consumer' => 'order_created',
        ]);

        // Verify OutboxEvent created (StockReserved)
        $this->assertDatabaseHas('outbox_events', [
            'event_type' => 'StockReserved',
            'correlation_id' => 'test-correlation-id',
        ]);
    }

    public function test_order_created_event_fails_if_insufficient_stock()
    {
        $product = Product::factory()->create([
            'stock_on_hand' => 2,
            'stock_reserved' => 0,
        ]);

        $orderUuid = (string) Str::uuid();
        $orderItemUuid = (string) Str::uuid();
        $eventId = (string) Str::uuid();

        $event = [
            'event_id' => $eventId,
            'correlation_id' => 'test-correlation-id',
            'data' => [
                'order_uuid' => $orderUuid,
                'items' => [
                    [
                        'product_uuid' => $product->uuid,
                        'order_item_uuid' => $orderItemUuid,
                        'quantity' => 5, // more than 2
                    ],
                ],
            ],
        ];

        /** @var OrderCreatedHandler $handler */
        $handler = app(OrderCreatedHandler::class);
        $handler->handle($event);

        // Verify Product stock NOT updated
        $product->refresh();
        $this->assertEquals(0, $product->stock_reserved);
        $this->assertEquals(2, $product->stock_available);

        // Verify StockReservation NOT created
        $this->assertDatabaseMissing('stock_reservations', [
            'order_uuid' => $orderUuid,
        ]);

        // Verify OutboxEvent created (StockFailed)
        $this->assertDatabaseHas('outbox_events', [
            'event_type' => 'StockFailed',
            'correlation_id' => 'test-correlation-id',
        ]);
    }

    public function test_order_created_event_is_idempotent()
    {
        $product = Product::factory()->create([
            'stock_on_hand' => 10,
        ]);

        $eventId = (string) Str::uuid();
        $event = [
            'event_id' => $eventId,
            'correlation_id' => 'test-correlation-id',
            'data' => [
                'order_uuid' => (string) Str::uuid(),
                'items' => [
                    [
                        'product_uuid' => $product->uuid,
                        'order_item_uuid' => (string) Str::uuid(),
                        'quantity' => 1,
                    ],
                ],
            ],
        ];

        /** @var OrderCreatedHandler $handler */
        $handler = app(OrderCreatedHandler::class);

        // First handle
        $handler->handle($event);
        $product->refresh();
        $this->assertEquals(1, $product->stock_reserved);

        // Second handle (same eventId)
        $handler->handle($event);
        $product->refresh();
        $this->assertEquals(1, $product->stock_reserved, 'Should not increment stock twice for same event');
    }
}
