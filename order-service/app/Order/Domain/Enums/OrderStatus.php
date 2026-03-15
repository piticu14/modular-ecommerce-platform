<?php

    namespace App\Order\Domain\Enums;

    enum OrderStatus: string
    {
        case PENDING = 'pending';
        case CONFIRMED = 'confirmed';
        case FAILED = 'failed';
        case CANCELLED = 'cancelled';

        public function isPending(): bool
        {
            return $this === self::PENDING;
        }

        public function isConfirmed(): bool
        {
            return $this === self::CONFIRMED;
        }

        public function isFailed(): bool
        {
            return $this === self::FAILED;
        }

        public function isCancelled(): bool
        {
            return $this === self::CANCELLED;
        }

        public function isFinal(): bool
        {
            return match ($this) {
                self::CONFIRMED,
                self::FAILED,
                self::CANCELLED => true,
                default => false,
            };
        }

        public function canBeCancelled(): bool
        {
            return $this === self::PENDING;
        }

        public static function activeStatuses(): array
        {
            return [
                self::PENDING,
            ];
        }
    }
