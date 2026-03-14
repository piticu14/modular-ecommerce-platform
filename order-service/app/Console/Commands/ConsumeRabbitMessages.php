<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MessageDispatcher;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeRabbitMessages extends Command
{
    protected $signature = 'rabbitmq:consume';

    public function handle(MessageDispatcher $dispatcher)
    {
        $connection = app('rabbitmq.connection');

        $channel = $connection->channel();

        $channel->basic_consume(
            'order-service.stock-events',
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $msg) use ($dispatcher) {

                $event = json_decode($msg->getBody(), true);

                $dispatcher->dispatch($event);

                $msg->ack();
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
