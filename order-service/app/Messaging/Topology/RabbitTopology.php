<?php

namespace App\Messaging\Topology;

use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitTopology
{
    public function declare(): void
    {
        /** @var array<int, array{host:string,port:int,user:string,password:string,vhost:string}> $config */
        $config = Config::get('queue.connections.rabbitmq.hosts', []);

        $connection = new AMQPStreamConnection(
            $config[0]['host'],
            $config[0]['port'],
            $config[0]['user'],
            $config[0]['password'],
            $config[0]['vhost']
        );

        $channel = $connection->channel();

        /*
        |--------------------------------------------------------------------------
        | Exchanges
        |--------------------------------------------------------------------------
        */

        $channel->exchange_declare(
            'ecommerce.events',
            'topic',
            false,
            true,
            false
        );

        $channel->exchange_declare(
            'ecommerce.retry',
            'topic',
            false,
            true,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | Main Queue
        |--------------------------------------------------------------------------
        */

        $channel->queue_declare(
            'order-service',
            false,
            true,
            false,
            false,
            false,
            [
                'x-dead-letter-exchange' => ['S', 'ecommerce.retry'],
                'x-dead-letter-routing-key' => ['S', 'order.retry'],
            ]
        );

        $channel->queue_bind(
            'order-service',
            'ecommerce.events',
            'order.*'
        );

        /*
        |--------------------------------------------------------------------------
        | Retry Queue
        |--------------------------------------------------------------------------
        */

        $channel->queue_declare(
            'order-service.retry',
            false,
            true,
            false,
            false,
            false,
            [
                'x-message-ttl' => ['I', 10000],
                'x-dead-letter-exchange' => ['S', 'ecommerce.events'],
            ]
        );

        $channel->queue_bind(
            'order-service.retry',
            'ecommerce.retry',
            'order.*'
        );

        /*
        |--------------------------------------------------------------------------
        | Dead Letter Queue
        |--------------------------------------------------------------------------
        */

        $channel->queue_declare(
            'order-service.dlq',
            false,
            true,
            false,
            false
        );

        $channel->queue_bind(
            'order-service.dlq',
            'ecommerce.retry',
            'order.*'
        );

        $channel->close();
        $connection->close();
    }
}
