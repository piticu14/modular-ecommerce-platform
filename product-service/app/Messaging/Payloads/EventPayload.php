<?php

    namespace App\Messaging\Payloads;

    use Carbon\CarbonInterface;

    abstract class EventPayload
    {
        protected static function envelope(
            string $eventId,
            string $eventType,
            string $source,
            CarbonInterface $occurredAt,
            string $correlationId,
            array $data
        ): array {
            return [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'event_version' => 1,
                'source' => $source,
                'occurred_at' => $occurredAt->toISOString(),
                'correlation_id' => $correlationId,
                'data' => $data,
            ];
        }
    }
