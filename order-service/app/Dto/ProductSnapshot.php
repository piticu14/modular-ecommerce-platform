<?php

namespace App\Data;

final readonly class ProductSnapshot
{
    public function __construct(
        public int $id,
        public string $name,
        public string $price,
        public string $currency,
    ) {
    }

    /**
     * @param array{id:int,name:string,price:string|int|float,currency:string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            price: (string) $data['price'],
            currency: (string) $data['currency'],
        );
    }
}
