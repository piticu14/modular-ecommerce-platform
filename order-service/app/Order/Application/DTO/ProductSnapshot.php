<?php

namespace App\Order\Application\DTO;

final readonly class ProductSnapshot
{
    public function __construct(
        public string $uuid,
        public string $name,
        public int $price,
        public string $currency
    ) {}

    /**
     * @param array{
     *     id:string,
     *     name:string,
     *     price:int,
     *     currency:string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: (string) $data['id'],
            name: (string) $data['name'],
            price: (int) $data['price'],
            currency: (string) $data['currency'],
        );
    }
}
