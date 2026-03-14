<?php


    namespace App\Jobs;

    use App\Messaging\Dispatching\MessageDispatcher;
    use Illuminate\Support\Facades\Log;
    use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

    class ProcessRabbitMQMessageJob extends BaseJob
    {
        public function fire()
        {
            $payload = null;

            try {

                $payload = $this->payload();

                Log::info('RabbitMQ message received', [
                    'event_id' => $payload['event_id'] ?? null,
                    'event_type' => $payload['event_type'] ?? null,
                    'occurred_at' => $payload['occurred_at'] ?? null,
                    'correlation_id' => $payload['correlation_id'] ?? null,
                    'data' => $payload['data'] ?? null,
                ]);

                app(MessageDispatcher::class)->dispatch($payload);

                $this->delete();

            } catch (\Throwable $e) {

                Log::error('RabbitMQ message failed', [
                    'event_id' => $payload['event_id'] ?? null,
                    'event_type' => $payload['event_type'] ?? null,
                    'correlation_id' => $payload['correlation_id'] ?? null,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }

        public function getName(): string
        {
            return 'rabbitmq.raw.message';
        }
    }
