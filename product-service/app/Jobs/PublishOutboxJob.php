<?php

namespace App\Jobs;

use App\Messaging\Infrastructure\Models\OutboxEvent;
use App\Messaging\Publishers\RabbitPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishOutboxJob implements ShouldQueue
{
    use Queueable;

    public function handle(RabbitPublisher $publisher): void
    {
        $events = OutboxEvent::whereNull('published_at')
            ->limit(100)
            ->get();

        foreach ($events as $event) {

            try {

                $publisher->publish($event);

                $event->update([
                    'published_at' => now(),
                ]);

            } catch (\Throwable $e) {

                $event->increment('attempts');

                throw $e;
            }
        }
    }
}
