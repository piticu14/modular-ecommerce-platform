<?php

    namespace App\Messaging\Dispatching;

    use App\Messaging\Consumers\OrderCreatedHandler;
    use Illuminate\Support\Facades\Redis;

    class MessageDispatcher
    {
        protected array $handlers = [
            'OrderCreated' => [
                OrderCreatedHandler::class,
            ],
        ];

        public function dispatch(array $event): void
        {
            $eventId = $event['event_id'] ?? null;

            if (!$eventId) {
                return;
            }

            $redisKey = "event:$eventId";


            if (!Redis::setnx($redisKey, 1)) {
                return;
            }

            Redis::expire($redisKey, 86400);

            $eventType = $event['event_type'] ?? null;

            if (!$eventType || !isset($this->handlers[$eventType])) {
                return;
            }

            foreach ($this->handlers[$eventType] as $handler) {
                app($handler)->handle($event);
            }
        }
    }
