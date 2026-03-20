<?php

namespace App\Messaging\Consumers;

use App\Messaging\Infrastructure\Models\ProcessedEvent;
use App\Stock\Application\Actions\ReserveStockAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCreatedHandler
{
    public function __construct(
        private ReserveStockAction $reserveStock
    ) {}

    public function handle(array $event): void
    {
        $eventId = $event['event_id'];

        DB::transaction(function () use ($event, $eventId) {

            $orderUuid = $event['data']['order_uuid'];

            $inserted = ProcessedEvent::insertOrIgnore([
                'event_id' => $eventId,
                'consumer' => 'order_created',
                'processed_at' => now(),
            ]);

            if ($inserted === 0) {
                return;
            }

            $this->reserveStock->handle($event);

            Log::info('OrderCreated received', [
                'order_uuid' => $orderUuid,
                'correlation_id' => $event['correlation_id'] ?? null,
            ]);
        });

    }
}
