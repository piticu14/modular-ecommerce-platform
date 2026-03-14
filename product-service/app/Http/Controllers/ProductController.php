<?php

    namespace App\Http\Controllers;

    use App\Models\Product;
    use Illuminate\Http\Request;

    class ProductController extends Controller
    {
        public function index(Request $request)
        {
            $ids = explode(',', $request->query('ids', ''));

            $products = Product::query()
                ->whereIn('id', $ids)
                ->get([
                    'id',
                    'name',
                    'price',
                    'currency',
                ]);

            return response()->json([
                'data' => $products,
            ]);
        }
    }
