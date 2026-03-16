<?php

    namespace App\Order\Infrastructure\Clients;

    use App\Order\Application\DTO\ProductSnapshot;
    use App\Order\Infrastructure\Exceptions\InvalidProductServiceResponseException;
    use App\Order\Infrastructure\Exceptions\ProductNotFoundException;
    use App\Order\Infrastructure\Exceptions\ProductServiceUnavailableException;
    use App\Support\InternalHttp;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\Log;

    class ProductServiceClient
    {
        /**
         * @param array<int,int> $ids
         * @return array<int,ProductSnapshot>
         */
        public function getProductsByUuid(array $uuids): array
        {
            $uuids = array_values(array_unique($uuids));

            if ($uuids === []) {
                return [];
            }


            try {

                $response = InternalHttp::get(
                    config('services.product_service.base_url'),
                    '/api/products/by-uuid',
                    [
                        'uuids' => implode(',', $uuids),
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

            Log::info('ProductService response', ['products' => $products]);

            if (!is_array($products)) {
                throw new InvalidProductServiceResponseException('Missing or invalid data key.');
            }

            $map = [];

            foreach ($products as $product) {
                $map[$product['uuid']] = ProductSnapshot::fromArray($product);
            }

            foreach ($uuids as $uuid) {
                if (!isset($map[$uuid])) {
                    throw new ProductNotFoundException($uuid);
                }
            }

            return $map;
        }
    }
