<?php

    namespace App\Http\Controllers\Api\V1;

    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class ProductController extends ApiController
    {
        /**
         * List products
         *
         * Returns paginated list of active products.
         *
         * @group Products
         * @authenticated
         *
         * @queryParam ids string Comma-separated list of product IDs (UUIDs) to filter by. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 200 {
         *   "data": [
         *     {
         *       "uuid": "9a2e88a5-0000-0000-0000-000000000000",
         *       "name": "Product name",
         *       "price": 100,
         *       "currency": "USD",
         *       "status": "ACTIVE",
         *       "stock_on_hand": 10,
         *       "stock_reserved": 2,
         *       "stock_available": 8,
         *       "created_at": "2023-10-27T12:00:00.000000Z",
         *       "updated_at": "2023-10-27T12:00:00.000000Z"
         *     }
         *   ]
         * }
         */
        public function index(Request $request): Response
        {
            return $this->forwardToService($request, 'products');
        }

        /**
         * Get product detail
         *
         * Returns details of a specific product by UUID.
         *
         * @group Products
         * @authenticated
         *
         * @urlParam id string required Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 200 {
         *   "data": {
         *     "uuid": "9a2e88a5-0000-0000-0000-000000000000",
         *     "name": "Product name",
         *     "price": 100,
         *     "currency": "USD",
         *     "status": "ACTIVE",
         *     "stock_on_hand": 10,
         *     "stock_reserved": 2,
         *     "stock_available": 8,
         *     "created_at": "2023-10-27T12:00:00.000000Z",
         *     "updated_at": "2023-10-27T12:00:00.000000Z"
         *   }
         * }
         *
         * @response 404 {
         *   "message": "Not Found"
         * }
         */
        public function show(Request $request, int|string $id): Response
        {
            return $this->forwardToService($request, 'products');
        }

        /**
         * Create product
         *
         * Creates new product.
         *
         * @group Products
         * @authenticated
         *
         * @bodyParam name string required Product name. Example: My Product
         * @bodyParam price number required Product price. Example: 99.99
         * @bodyParam currency string required 3-letter currency code. Example: USD
         * @bodyParam stock_on_hand integer Initial stock count. Example: 100
         * @bodyParam uuid string Optional UUID for the product. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 201 {
         *   "data": {
         *     "uuid": "9a2e88a5-0000-0000-0000-000000000000",
         *     "name": "My Product",
         *     "price": 99.99,
         *     "currency": "USD",
         *     "status": "ACTIVE",
         *     "stock_on_hand": 100,
         *     "stock_reserved": 0,
         *     "stock_available": 100,
         *     "created_at": "2023-10-27T12:00:00.000000Z",
         *     "updated_at": "2023-10-27T12:00:00.000000Z"
         *   }
         * }
         *
         * @response 422 {
         *   "message": "The name field is required.",
         *   "errors": {
         *     "name": ["The name field is required."]
         *   }
         * }
         */
        public function store(Request $request): Response
        {
            return $this->forwardToService($request, 'products');
        }

        /**
         * Delete product
         *
         * Archives product by UUID.
         *
         * @group Products
         * @authenticated
         *
         * @urlParam id string required Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 204
         *
         * @response 409 {
         *   "message": "Product is already archived"
         * }
         */
        public function destroy(Request $request, int|string $id): Response
        {
            return $this->forwardToService($request, 'products');
        }

        /**
         * Product stock reservations
         *
         * Returns stock reservations for a product.
         *
         * @group Products
         * @authenticated
         *
         * @urlParam product string required Product UUID. Example: 9a2e88a5-0000-0000-0000-000000000000
         *
         * @response 200 {
         *   "data": [
         *     {
         *       "id": 1,
         *       "order_uuid": "9a2e88a5-0000-0000-0000-000000000000",
         *       "order_item_uuid": "8b3f99b6-0000-0000-0000-000000000000",
         *       "product_id": 1,
         *       "quantity": 2,
         *       "status": "reserved",
         *       "created_at": "2023-10-27T12:00:00.000000Z",
         *       "updated_at": "2023-10-27T12:00:00.000000Z"
         *     }
         *   ]
         * }
         */
        public function stockReservations(Request $request, int|string $product): Response
        {
            return $this->forwardToService($request, 'products');
        }
    }
