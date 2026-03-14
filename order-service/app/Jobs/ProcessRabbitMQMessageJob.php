<?php


    namespace App\Queue\Jobs;

    use MessageDispatcher;
    use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;

    class ProcessRabbitMQMessageJob extends RabbitMQJob
    {
        public function fire()
        {
            try {

                $payload = json_decode($this->getRawBody(), true);

                app(MessageDispatcher::class)->dispatch($payload);

                $this->delete();

            } catch (\Throwable $e) {

                $this->release(10);

                throw $e;
            }
        }
    }
