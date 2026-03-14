<?php

    namespace app\Messaging\Publishers;
    use Queue;

    class RabbitPublisher
    {
        public function publish(string $routingKey, array $event): void
        {
            Queue::connection('rabbitmq')->pushRaw(
                json_encode($event),
                '',
                [
                    'exchange' => 'ecommerce.events',
                    'routing_key' => $routingKey,
                ]
            );
        }
    }
