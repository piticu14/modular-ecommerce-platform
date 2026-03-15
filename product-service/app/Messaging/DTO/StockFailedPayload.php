<?php

    namespace App\Messaging\DTO;

    use Carbon\CarbonInterface;
    use Illuminate\Support\Collection;

    class StockFailedPayload extends EventPayload
    {
        public static function build(
            int $orderId,
            string $eventId,
            CarbonInterface $occurredAt,
            string $correlationId,
            Collection $items
        ): array
    {
        return self::envelope(
            $eventId,
            'StockFailed',
            'product-service',
            $occurredAt,
            $correlationId,
            [
                'order_id' => $orderId,
                'items' => $items->values()->all(),
            ]
        );
    }
    }

