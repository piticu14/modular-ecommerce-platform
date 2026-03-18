<?php

namespace App\Messaging\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class OutboxEvent extends Model
{
    protected $fillable = [
        'id',
        'event_type',
        'payload',
        'routing_key',
        'occurred_at',
        'published_at',
        'correlation_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'published_at' => 'datetime',
    ];
}
