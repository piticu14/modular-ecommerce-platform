<?php

    namespace App\Order\Infrastructure\Clients;

    use App\Order\Application\DTO\ProductSnapshot;
    use App\Order\Infrastructure\Exceptions\InvalidProductServiceResponseException;
    use App\Order\Infrastructure\Exceptions\ProductNotFoundException;
    use App\Order\Infrastructure\Exceptions\ProductServiceUnavailableException;
    use App\Support\InternalHttp;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;

    class ProductServiceClient
    {
        /**
         * @param array<int,int> $ids
         * @return array<int,ProductSnapshot>
         */
        public function getProducts(array $ids): array
        {
            $ids = array_values(array_unique(array_map('intval', $ids)));

            if ($ids === []) {
                return [];
            }


            try {

                $response = InternalHttp::get(
                    config('services.product_service.base_url'),
                    '/api/products',
                    [
                        'ids' => implode(',', $ids),
                    ]
                )->throw();

            } catch (ConnectionException|RequestException $e) {

                throw new ProductServiceUnavailableException(
                    message: 'ProductService is unavailable.',
                    previous: $e
                );
            }

            /** @var array<int,array{id:int,name:string,price:string|int|float,currency:string}> $products */
            $products = $response->json('data', []);

            if (!is_array($products)) {
                throw new InvalidProductServiceResponseException('Missing or invalid data key.');
            }

            $map = [];

            foreach ($products as $product) {
                $map[(int) $product['id']] = ProductSnapshot::fromArray($product);
            }

            foreach ($ids as $id) {
                if (!isset($map[$id])) {
                    throw new ProductNotFoundException($id);
                }
            }

            return $map;
        }
    }
