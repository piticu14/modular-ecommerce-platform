<?php

    namespace App\Messaging\Infrastructure\Models;

    use Illuminate\Database\Eloquent\Model;

    class ProcessedEvent extends Model
    {
        public $timestamps = false;

        protected $primaryKey = 'event_id';

        public $incrementing = false;

        protected $keyType = 'string';

        protected $fillable = [
            'event_id',
            'consumer',
            'processed_at'
        ];
    }
