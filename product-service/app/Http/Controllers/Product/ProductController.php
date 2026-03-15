<?php

    namespace App\Http\Controllers\Product;

    use App\Http\Controllers\Controller;
    use App\Http\Resources\Product\ProductResource;
    use App\Product\Domain\Models\Product;
    use Illuminate\Http\Request;

    class ProductController extends Controller
    {
        public function index(Request $request)
        {
            $ids = array_filter(explode(',', $request->query('ids', '')));

            $products = Product::query()
                ->when($ids, fn ($q) => $q->whereIn('id', $ids))
                ->get();

            return ProductResource::collection($products);
        }

        public function show(Product $product)
        {
            return new ProductResource($product);
        }
    }
