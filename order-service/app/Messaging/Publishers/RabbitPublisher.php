<?php

    namespace App\Messaging\Publishers;

    use App\Models\OutboxEvent;
    use Illuminate\Support\Facades\Log;
    use Queue;

    class RabbitPublisher
    {
        public function publish(OutboxEvent $event): void
        {
            Queue::connection('rabbitmq')->pushRaw(
                payload: json_encode($event->payload, JSON_THROW_ON_ERROR),
                queue: $event->routing_key,
                options: [
                    'exchange' => 'ecommerce.events',
                    'routing_key' => $event->routing_key,
                    'headers' => [
                        'event_type' => $event->event_type,
                        'correlation_id' => $event->correlation_id,
                    ],
                ],
            );

            Log::info('Outbox event published', [
                'event_id' => $event->id,
                'event_type' => $event->event_type,
                'occurred_at' => $event->occurred_at?->toISOString(),
                'correlation_id' => $event->correlation_id,
                'payload' => $event->payload,
            ]);
        }
    }
