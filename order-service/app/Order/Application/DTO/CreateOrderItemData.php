<?php

namespace App\Order\Application\DTO;

final readonly class CreateOrderItemData
{
    public function __construct(
        public string $productUuid,
        public int $quantity,
    ) {}

    public static function from(array $data): self
    {
        return new self(
            productUuid: $data['product_uuid'],
            quantity: (int) $data['quantity'],
        );
    }
}
