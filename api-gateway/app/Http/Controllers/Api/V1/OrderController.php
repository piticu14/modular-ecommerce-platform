<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Resources\Api\V1\Common\ErrorResource;
use App\Http\Resources\Api\V1\Order\OrderResource;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response as DedocResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends ApiController
{
    /**
     * List orders
     *
     * Returns a list of authenticated user's orders.
     *
     * @authenticated
     */
    #[DedocResponse(200, 'OK', type: 'array<'.OrderResource::class.'>')]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Orders')]
    public function index(Request $request): Response
    {
        return $this->forwardToService($request, 'orders');
    }

    /**
     * Get order detail
     *
     * Returns details of a specific order by UUID.
     *
     * @authenticated
     *
     * @param  string  $id  Order UUID (e.g. 9a2e88a5-0000-0000-0000-000000000000)
     *
     * @psalm-suppress PossiblyUnusedParam $id is required by route and forwarded via Request
     */
    #[DedocResponse(200, 'OK', type: OrderResource::class)]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(404, 'Not found', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Orders')]
    public function show(Request $request, string $id): Response
    {
        return $this->forwardToService($request, 'orders');
    }

    /**
     * Create order
     *
     * Creates a new order for the authenticated user.
     *
     * @authenticated
     */
    #[DedocResponse(201, 'Created', type: OrderResource::class)]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(422, 'Unprocessable Content', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Orders')]
    public function store(StoreOrderRequest $request): Response
    {
        return $this->forwardToService($request, 'orders');
    }

    /**
     * Delete order
     *
     * Cancels an order by UUID.
     *
     * @authenticated
     *
     * @param  string  $id  Order UUID (e.g. 9a2e88a5-0000-0000-0000-000000000000)
     *
     * @psalm-suppress PossiblyUnusedParam $id is required by route and forwarded via Request
     */
    #[DedocResponse(204, 'No Content')]
    #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
    #[DedocResponse(404, 'Not Found', type: ErrorResource::class)]
    #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
    #[Group('Orders')]
    public function destroy(Request $request, string $id): Response
    {
        return $this->forwardToService($request, 'orders');
    }
}
