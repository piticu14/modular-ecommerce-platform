<?php

namespace App\Jobs;

use app\Messaging\Publishers\RabbitPublisher;
use App\Models\OutboxEvent;
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

                $publisher->publish(
                    $event->routing_key,
                    $event->payload
                );

                $event->update([
                    'published_at' => now()
                ]);

            } catch (\Throwable $e) {

                $event->increment('attempts');

                throw $e;
            }
        }
    }
}
