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
     * @param  array<int,string>  $uuids
     * @return array<string,ProductSnapshot>
     */
    public function getProductsByUuid(array $uuids): array
    {

        $uuids = array_values(array_unique($uuids));

        if ($uuids === []) {
            return [];
        }

        $baseUrl = config('services.product_service.base_url');

        if (! is_string($baseUrl) || $baseUrl === '') {
            throw new \RuntimeException('Product service Base url is not configured.');
        }

        try {

            $response = InternalHttp::get(
                $baseUrl,
                '/api/v1/products/by-uuid',
                [
                    'uuids' => implode(',', $uuids),
                ]
            )->throw();

        } catch (ConnectionException $e) {

            Log::error('ProductService connection failed', [
                'message' => $e->getMessage(),
                'url' => $baseUrl.'/api/v1/products/by-uuid',
            ]);

            throw new ProductServiceUnavailableException(
                message: 'Product service is not reachable.',
                previous: $e
            );

        } catch (RequestException $e) {

            Log::error('ProductService returned error response', [
                'status' => $e->response->status(),
                'body' => $e->response->body(),
                'url' => $baseUrl.'/api/products/by-uuid',
            ]);

            throw new InvalidProductServiceResponseException(
                message: 'Product service returned invalid response.',
                previous: $e
            );
        }

        $rawData = $response->json('data', []);

        Log::info('ProductService response', ['products' => $rawData]);

        if (! is_array($rawData)) {
            throw new InvalidProductServiceResponseException('Missing or invalid data key.');
        }

        /**
         * @var array<int,array{
         *     id:string,
         *     name:string,
         *     price:int,
         *     currency:string,
         * }> $products
         */
        $products = $rawData;
        $map = [];

        foreach ($products as $product) {
            $map[$product['id']] = ProductSnapshot::fromArray($product);
        }

        foreach ($uuids as $uuid) {
            if (! isset($map[$uuid])) {
                throw new ProductNotFoundException($uuid);
            }
        }

        return $map;
    }
}
