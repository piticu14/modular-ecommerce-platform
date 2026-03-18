<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Resources\Stock\StockReservationResource;
use App\Product\Domain\Models\Product;
use App\Stock\Domain\Models\StockReservation;

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
