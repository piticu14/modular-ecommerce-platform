<?php

    namespace Dto;

    final readonly class CreateOrderItemData
    {
        public function __construct(
            public int $productId,
            public int $quantity,
        ) {
        }

        public static function from(array $data): self
        {
            return new self(
                productId: (int) $data['product_id'],
                quantity: (int) $data['quantity'],
            );
        }
    }
