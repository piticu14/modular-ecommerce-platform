<?php

    namespace App\Dto;

    final readonly class CreateOrderItemData
    {
        public function __construct(
            public int $productId,
            public int $quantity,
        ) {
        }
    }
