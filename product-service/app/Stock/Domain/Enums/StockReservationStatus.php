<?php

    namespace App\Stock\Domain\Enums;

    enum StockReservationStatus: string
    {
        case RESERVED = 'reserved';
        case RELEASED = 'released';
        case CONSUMED = 'consumed';

        public function isActive(): bool
        {
            return $this === self::RESERVED;
        }

        public function isReleased(): bool
        {
            return $this === self::RELEASED;
        }

        public function isConsumed(): bool
        {
            return $this === self::CONSUMED;
        }



        public static function activeStatuses(): array
        {
            return [
                self::RESERVED,
            ];
        }
    }
