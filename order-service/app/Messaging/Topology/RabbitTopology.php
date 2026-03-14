<?php


    use Illuminate\Support\Facades\Config;
    use PhpAmqpLib\Connection\AMQPStreamConnection;

    class RabbitTopology
    {
        public function declare(): void
        {
            $config = Config::get('queue.connections.rabbitmq.hosts')[0];

            $connection = new AMQPStreamConnection(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['password'],
                $config['vhost']
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
                    'x-dead-letter-routing-key' => ['S', 'order.retry']
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
                    'x-dead-letter-exchange' => ['S', 'ecommerce.events']
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
