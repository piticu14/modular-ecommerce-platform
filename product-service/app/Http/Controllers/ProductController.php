<?php

    namespace App\Http\Controllers;

    use App\Http\Resources\ProductResource;
    use App\Models\Product;
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
