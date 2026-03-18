<?php

namespace App\Order\Application\DTO;

final readonly class ProductSnapshot
{
    public function __construct(
        public string $uuid,
        public string $name,
        public int $price,
        public string $currency,
        public string $status,
        public int $stock_on_hand,
        public int $stock_reserved,
        public int $stock_available,
    ) {}

    /**
     * @param array{
     *     uuid:string,
     *     name:string,
     *     price:int,
     *     currency:string,
     *     status:string,
     *     stock_on_hand:int,
     *     stock_reserved:int,
     *     stock_available:int,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            uuid: (string) $data['uuid'],
            name: (string) $data['name'],
            price: (int) $data['price'],
            currency: (string) $data['currency'],
            status: (string) $data['status'],
            stock_on_hand: (int) $data['stock_on_hand'],
            stock_reserved: (int) $data['stock_reserved'],
            stock_available: (int) $data['stock_available'],
        );
    }
}
