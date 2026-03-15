<?php

    namespace App\Order\Infrastructure\Exceptions;

    use RuntimeException;

    final class ProductNotFoundException extends RuntimeException
    {
        public function __construct(int $productId)
        {
            parent::__construct("Product {$productId} was not found in ProductService.");
        }
    }
