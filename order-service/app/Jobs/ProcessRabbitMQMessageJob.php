<?php


    namespace App\Jobs;

    use App\Messaging\Dispatcher\MessageDispatcher;
    use Illuminate\Support\Facades\Log;
    use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

    class ProcessRabbitMQMessageJob extends BaseJob
    {
        private const MAX_RETRIES = 5;

        public function fire()
        {
            $payload = $this->payload();

            try {

                app(MessageDispatcher::class)->dispatch($payload);

                $this->delete();

            } catch (\Throwable $e) {

                $attempt = $this->rabbitAttempts();

                Log::warning('RabbitMQ retry attempt', [
                    'attempt' => $attempt,
                    'max_retries' => self::MAX_RETRIES,
                    'event_id' => $payload['event_id'] ?? null,
                    'error' => $e->getMessage()
                ]);

                if ($attempt >= self::MAX_RETRIES) {

                    $this->getRabbitMQ()->pushRaw(
                        $this->getRawBody(),
                        'order-service.dlq'
                    );

                    $this->delete();

                    Log::warning('Message moved to DLQ', [
                        'attempts' => $attempt
                    ]);

                    return;
                }

                $this->getRabbitMQ()->reject($this);

            }
        }

        private function rabbitAttempts(): int
        {
            $headers = $this->getRabbitMQMessage()->get_properties()['application_headers'] ?? null;

            if (!$headers) {
                return 0;
            }

            $data = $headers->getNativeData();

            return $data['x-death'][0]['count'] ?? 0;
        }

        public function getName(): string
        {
            return 'rabbitmq.raw.message';
        }
    }
