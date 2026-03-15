<?php

    namespace App\Product\Domain\Enums;

    enum ProductStatus: string
    {
        case ACTIVE = 'active';
        case INACTIVE = 'inactive';
        case ARCHIVED = 'archived';

        public function isActive(): bool
        {
            return $this === self::ACTIVE;
        }

        public function isInactive(): bool
        {
            return $this === self::INACTIVE;
        }

        public function isArchived(): bool
        {
            return $this === self::ARCHIVED;
        }

        public function isFinal(): bool
        {
            return $this === self::ARCHIVED;
        }

        public static function activeStatuses(): array
        {
            return [
                self::ACTIVE,
            ];
        }
    }
