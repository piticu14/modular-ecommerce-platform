<?php

    namespace App\Http\Controllers\Api\V1;

    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class OrderController extends ApiController
    {
        /**
         * List orders
         *
         * Returns a list of authenticated user's orders.
         *
         * @group Orders
         * @authenticated
         *
         * @response 200 {
         *   "data": [
         *     {
         *       "id": "9a2e88a5-0000-0000-0000-000000000000",
         *       "status": "PENDING",
         *       "currency": "USD",
         *       "subtotal": 199.98,
         *       "total": 199.98,
         *       "items": [
         *         {
         *           "product_uuid": "8b3f99b6-0000-0000-0000-000000000000",
         *           "product_name": "My Product",
         *           "price": 99.99,
         *           "currency": "USD",
         *           "quantity": 2,
         *           "line_total": 199.98
         *         }
         *       ],
         *       "created_at": "2023-10-27T12:00:00.000000Z"
         *     }
         *   ]
         * }
         */
        public function index(Request $request): Response
        {
            return $this->forwardToService($request, 'orders');
        }

        /**
         * Get order detail
         *
         * Returns details of a specific order by UUID.
         *
         * @group Orders
         * @authenticated
         *
         * @urlParam id string required Order UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 200 {
         *   "data": {
         *     "id": "9a2e88a5-0000-0000-0000-000000000000",
         *     "status": "CONFIRMED",
         *     "currency": "USD",
         *     "subtotal": 199.98,
         *     "total": 199.98,
         *     "items": [
         *         {
         *           "product_uuid": "8b3f99b6-0000-0000-0000-000000000000",
         *           "product_name": "My Product",
         *           "price": 99.99,
         *           "currency": "USD",
         *           "quantity": 2,
         *           "line_total": 199.98
         *         }
         *     ],
         *     "created_at": "2023-10-27T12:00:00.000000Z"
         *   }
         * }
         *
         * @response 404 {
         *   "message": "Not Found"
         * }
         */
        public function show(Request $request, int|string $id): Response
        {
            return $this->forwardToService($request, 'orders');
        }

        /**
         * Create order
         *
         * Creates a new order for the authenticated user.
         *
         * @group Orders
         * @authenticated
         *
         * @bodyParam items array required List of items to order.
         * @bodyParam items[].product_uuid string required Product UUID. Example: 8b3f99b6-0000-0000-0000-000000000000
         * @bodyParam items[].quantity integer required Quantity (1-100). Example: 2
         *
         * @response 201 {
         *   "data": {
         *     "id": "9a2e88a5-0000-0000-0000-000000000000",
         *     "status": "PENDING",
         *     "currency": "USD",
         *     "subtotal": 199.98,
         *     "total": 199.98,
         *     "items": [
         *         {
         *           "product_uuid": "8b3f99b6-0000-0000-0000-000000000000",
         *           "product_name": "My Product",
         *           "price": 99.99,
         *           "currency": "USD",
         *           "quantity": 2,
         *           "line_total": 199.98
         *         }
         *     ],
         *     "created_at": "2023-10-27T12:00:00.000000Z"
         *   }
         * }
         *
         * @response 503 {
         *   "message": "Order could not be created"
         * }
         *
         * @response 422 {
         *   "message": "The items field is required.",
         *   "errors": {
         *     "items": ["The items field is required."]
         *   }
         * }
         */
        public function store(Request $request): Response
        {
            return $this->forwardToService($request, 'orders');
        }

        /**
         * Delete order
         *
         * Cancels an order by UUID.
         *
         * @group Orders
         * @authenticated
         *
         * @urlParam id string required Order UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 204
         *
         * @response 409 {
         *   "message": "Order is already final and cannot be cancelled"
         * }
         */
        public function destroy(Request $request, int|string $id): Response
        {
            return $this->forwardToService($request, 'orders');
        }
    }
