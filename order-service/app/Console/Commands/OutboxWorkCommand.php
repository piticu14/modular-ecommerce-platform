<?php

    namespace App\Console\Commands;

    use App\Messaging\Publishers\RabbitPublisher;
    use App\Models\OutboxEvent;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\DB;

    class OutboxWorkCommand extends Command
    {
        protected $signature = 'outbox:work';

        protected bool $running = true;

        public function handle(RabbitPublisher $publisher): int
        {
            $this->info('Outbox worker started');

            pcntl_async_signals(true);

            pcntl_signal(SIGTERM, fn() => $this->shutdown());
            pcntl_signal(SIGINT, fn() => $this->shutdown());

            while ($this->running) {

                $events = DB::transaction(function () {

                    return OutboxEvent::query()
                        ->whereNull('published_at')
                        ->orderBy('occurred_at')
                        ->limit(100)
                        ->lock('FOR UPDATE SKIP LOCKED')
                        ->get();

                });

                if ($events->isEmpty()) {
                    usleep(200000); // 200ms
                    continue;
                }

                foreach ($events as $event) {

                    try {

                        logger()->info('Publishing event', [
                            'event_id' => $event->id,
                            'event_type' => $event->event_type,
                            'routing_key' => $event->routing_key,
                        ]);

                        $publisher->publish($event);

                        $event->update([
                            'published_at' => now(),
                        ]);

                    } catch (\Throwable $e) {

                        $event->increment('attempts');

                        logger()->error('Outbox publish failed', [
                            'event_id' => $event->id,
                            'error' => $e->getMessage(),
                        ]);

                    }
                }

            }

            return 0;
        }

        protected function shutdown(): void
        {
            logger()->info('Outbox worker shutting down');
            $this->running = false;
        }
    }
