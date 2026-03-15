<?php

    namespace App\Http\Controllers\Product;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Product\StoreProductRequest;
    use App\Http\Resources\Product\ProductResource;
    use App\Product\Domain\Enums\ProductStatus;
    use App\Product\Domain\Exceptions\ProductAlreadyArchivedException;
    use App\Product\Domain\Models\Product;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\ResourceCollection;

    class ProductController extends Controller
    {
        public function index(Request $request): ResourceCollection
        {
            $ids = array_filter(explode(',', $request->query('ids', '')));

            $products = Product::query()
                ->where('status', ProductStatus::ACTIVE)
                ->when($ids, fn ($q) => $q->whereIn('id', $ids))
                ->get();

            return ProductResource::collection($products);
        }

        public function indexByUuid(Request $request): ResourceCollection
        {
            $uuids = array_filter(explode(',', $request->query('uuids', '')));

            $products = Product::query()
                ->when($uuids, fn ($q) => $q->whereIn('uuid', $uuids))
                ->get();

            return ProductResource::collection($products);
        }

        public function show(Product $product): ProductResource
        {
            return new ProductResource($product);
        }

        public function store(StoreProductRequest $request): JsonResponse
        {

            $product = Product::create([
                ...$request->validated(),
                'stock_on_hand' => $request->input('stock_on_hand', 0),
                'stock_reserved' => 0,
                'status' => ProductStatus::ACTIVE,
            ]);

            return (new ProductResource($product))
                ->response()
                ->setStatusCode(201);
        }

        public function destroy(Product $product): JsonResponse
        {

            try {

                $product->archive();

            } catch (ProductAlreadyArchivedException $e) {

                return response()->json([
                    'message' => $e->getMessage()
                ], 409);
            }


            return response()->json([], 204);
        }
    }
