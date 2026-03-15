<?php

    namespace App\Http\Controllers;

    use App\Http\Resources\StockReservationResource;
    use App\Models\Product;
    use App\Models\StockReservation;

    class StockReservationController extends Controller
    {

        public function index(Product $product)
        {
            $reservations = StockReservation::query()
                ->where('product_id', $product->id)
                ->latest()
                ->get();

            return StockReservationResource::collection($reservations);
        }
    }
