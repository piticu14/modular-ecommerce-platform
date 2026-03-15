<?php

    namespace App\Http\Controllers\Product;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Product\StoreProductRequest;
    use App\Http\Resources\Product\ProductResource;
    use App\Product\Domain\Enums\ProductStatus;
    use App\Product\Domain\Models\Product;
    use Illuminate\Http\Request;

    class ProductController extends Controller
    {
        public function index(Request $request)
        {
            $ids = array_filter(explode(',', $request->query('ids', '')));

            $products = Product::query()
                ->where('status', ProductStatus::ACTIVE)
                ->when($ids, fn ($q) => $q->whereIn('id', $ids))
                ->get();

            return ProductResource::collection($products);
        }

        public function show(Product $product)
        {
            return new ProductResource($product);
        }

        public function store(StoreProductRequest $request)
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

        public function destroy(Product $product)
        {
            if ($product->status === ProductStatus::ARCHIVED) {
                return response()->json([
                    'message' => 'Product already archived.'
                ], 409);
            }

            $product->update([
                'status' => ProductStatus::ARCHIVED,
            ]);

            return response()->noContent();
        }
    }
