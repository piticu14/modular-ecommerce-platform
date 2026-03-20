<?php

namespace App\Messaging\Dispatcher;

use App\Messaging\Consumers\OrderCreatedHandler;
use Illuminate\Support\Facades\Redis;

class MessageDispatcher
{
    protected array $handlers = [
        'OrderCreated' => [
            OrderCreatedHandler::class,
        ],
    ];

    /**
     * @param array{
     *     event_id?: string,
     *     event_type?: string,
     *     event_version: int,
     *     source: string,
     *     occurred_at: string,
     *     correlation_id: string|null,
     *     data: array<string, mixed>
     * } $event
     *
     * @throws \Exception
     */
    public function dispatch(array $event): void
    {
        $eventId = $event['event_id'];

        if (! $eventId) {
            return;
        }


        $eventType = $event['event_type'];

        if (! $eventType || ! isset($this->handlers[$eventType])) {
            return;
        }

        foreach ($this->handlers[$eventType] as $handler) {
            app($handler)->handle($event);
        }
    }
}
