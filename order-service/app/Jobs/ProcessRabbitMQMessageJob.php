<?php


    namespace App\Jobs;

    use App\Messaging\Dispatching\MessageDispatcher;
    use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

    class ProcessRabbitMQMessageJob extends BaseJob
    {
        public function fire()
        {
            try {

                $payload = $this->payload();

                app(MessageDispatcher::class)->dispatch($payload);

                $this->delete();

            } catch (\Throwable $e) {

                $this->release(10);

                throw $e;
            }
        }
    }
