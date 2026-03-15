<?php

    namespace App\Order\Infrastructure\Exceptions;

    use RuntimeException;

    final class ProductNotFoundException extends RuntimeException
    {
        public function __construct(string $productUuid)
        {
            parent::__construct("Product {$productUuid} was not found in ProductService.");
        }
    }
