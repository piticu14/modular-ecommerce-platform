<?php

    namespace App\Services;

    use App\Data\ProductSnapshot;
    use App\Exceptions\InvalidProductServiceResponseException;
    use App\Exceptions\ProductNotFoundException;
    use App\Exceptions\ProductServiceUnavailableException;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Str;
    use Throwable;

    class ProductServiceClient
    {
        /**
         * @param array<int, int> $ids
         * @return array<int, ProductSnapshot>
         */
        public function getProducts(array $ids): array
        {
            $ids = array_values(array_unique(array_map('intval', $ids)));

            if ($ids === []) {
                return [];
            }

            try {
                $response = Http::baseUrl(config('services.product_service.url'))
                    ->acceptJson()
                    ->asJson()
                    ->timeout((int) config('services.product_service.timeout', 2))
                    ->retry(2, 150, function (Throwable $exception): bool {
                        return $exception instanceof ConnectionException;
                    })
                    ->withHeaders([
                        'X-Correlation-ID' => request()->header('X-Correlation-ID', (string) Str::uuid()),
                    ])
                    ->get('/products', [
                        'ids' => implode(',', $ids),
                    ]);

                $response->throw();
            } catch (ConnectionException|RequestException $e) {
                throw new ProductServiceUnavailableException(
                    previous: $e,
                    message: 'ProductService is unavailable.'
                );
            }

            /** @var array<int, array{id:int,name:string,price:string|int|float,currency:string}> $products */
            $products = $response->json('data', []);

            if (!is_array($products)) {
                throw new InvalidProductServiceResponseException('Missing or invalid data key.');
            }

            $map = [];

            foreach ($products as $product) {
                $map[(int) $product['id']] =  ProductSnapshot::fromArray($product);
            }

            foreach ($ids as $id) {
                if (!isset($map[$id])) {
                    throw new ProductNotFoundException($id);
                }
            }

            return $map;
        }
    }
