<?php

    namespace App\Services;

    use App\Dto\ProductSnapshot;
    use App\Exceptions\InvalidProductServiceResponseException;
    use App\Exceptions\ProductNotFoundException;
    use App\Exceptions\ProductServiceUnavailableException;
    use App\Support\InternalHttp;
    use App\Support\InternalRequestSigner;
    use App\Support\RequestContext;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\Http;
    use Throwable;

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
