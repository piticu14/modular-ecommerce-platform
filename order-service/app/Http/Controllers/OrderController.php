<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreOrderRequest;
    use App\Http\Resources\OrderResource;
    use App\Order\Application\Actions\CreateOrderAction;
    use App\Order\Application\Exceptions\OrderCreationFailedException;
    use App\Support\RequestContext;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Http\JsonResponse;
    use Order;
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

            try {

                $order = $action->execute(
                    items: $request->validated('items'),
                    userId: RequestContext::userId(),
                );

            } catch (OrderCreationFailedException $e) {

                return response()->json([
                    'message' => 'Order could not be created'
                ], 503);

            }

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
