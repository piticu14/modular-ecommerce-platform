<?php

    namespace App\Http\Controllers;

    use App\Actions\CreateOrderAction;
    use App\Http\Requests\StoreOrderRequest;
    use App\Http\Resources\OrderResource;
    use App\Models\Order;
    use App\Support\RequestContext;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Http\JsonResponse;
    use Throwable;

    class OrderController extends Controller
    {

        /**
         * @throws RequestException
         * @throws Throwable
         * @throws ConnectionException
         */
        public function store(
            StoreOrderRequest $request,
            CreateOrderAction $action
        ): JsonResponse {

            $order = $action->execute(
                items: $request->validated('items'),
                userId: RequestContext::userId(),
            );

            return response()->json([
                'data' => new OrderResource($order)
            ], 201);
        }

        public function show(Order $order): JsonResponse
        {
            $order->load('items');

            return response()->json([
                'data' => new OrderResource($order)
            ]);
        }
    }
