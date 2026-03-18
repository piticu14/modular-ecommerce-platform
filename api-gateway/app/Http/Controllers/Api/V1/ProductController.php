<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Product\ProductIndexRequest;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Resources\Api\V1\Common\ErrorResource;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Http\Resources\Api\V1\Product\StockReservationResource;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response as DedocResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends ApiController
{
    /**
     * List products
     *
     * Returns a paginated list of active products.
     *
     * @authenticated
     */
    #[DedocResponse(200, 'OK', type: 'array<'.ProductResource::class.'>')]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Products')]
    public function index(ProductIndexRequest $request): Response
    {

        return $this->forwardToService($request, 'products');
    }

    /**
     * Get product detail
     *
     * Returns details of a specific product by UUID.
     *
     * @authenticated
     *
     * @param  string  $id  Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
     *
     * @psalm-suppress PossiblyUnusedParam $id is required by route and forwarded via Request
     */
    #[DedocResponse(200, 'OK', type: ProductResource::class)]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(404, 'Not found', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Products')]
    public function show(Request $request, string $id): Response
    {
        return $this->forwardToService($request, 'products');
    }

    /**
     * Create product
     *
     * Creates new product.
     *
     * @authenticated
     */
    #[DedocResponse(201, 'Created', type: ProductResource::class)]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(422, 'Unprocessable Content', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Products')]
    public function store(StoreProductRequest $request): Response
    {
        return $this->forwardToService($request, 'products');
    }

    /**
     * Delete product
     *
     * Archives product by UUID.
     *
     * @authenticated
     *
     * @param  string  $id  Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
     */
    #[DedocResponse(204, 'No Content')]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(404, 'Not Found', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Products')]
    public function destroy(Request $request, string $id): Response
    {
        return $this->forwardToService($request, 'products');
    }

    /**
     * Product stock reservations
     *
     * Returns stock reservations for a product.
     *
     * @authenticated
     *
     * @param  string  $id  Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
     *
     * @psalm-suppress PossiblyUnusedParam $id is required by route and forwarded via Request
     */
    #[DedocResponse(200, 'OK', type: StockReservationResource::class)]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(404, 'Not Found', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Products')]
    public function stockReservations(Request $request, string $id): Response
    {
        return $this->forwardToService($request, 'products');
    }
}
