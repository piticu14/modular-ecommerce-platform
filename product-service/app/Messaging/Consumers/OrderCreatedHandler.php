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

            $exists = ProcessedEvent::where([
                'event_id' => $eventId,
                'consumer' => 'order_created',
            ])->lockForUpdate()->exists();

            if ($exists) {
                return;
            }

            $this->reserveStock->handle($event);

            ProcessedEvent::create([
                'event_id' => $eventId,
                'consumer' => 'order_created',
                'processed_at' => now(),
            ]);

            Log::info('OrderCreated received', [
                'order_uuid' => $orderUuid,
                'correlation_id' => $event['correlation_id'] ?? null,
            ]);
        });

    }
}
