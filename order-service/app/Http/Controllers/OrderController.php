<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\StoreOrderRequest;
    use App\Http\Resources\OrderResource;
    use App\Order\Application\Actions\CreateOrderAction;
    use App\Order\Application\Exceptions\OrderCreationFailedException;
    use App\Order\Domain\Enums\OrderStatus;
    use App\Order\Domain\Exceptions\OrderAlreadyFinalException;
    use App\Order\Domain\Models\Order;
    use App\Support\RequestContext;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Resources\Json\ResourceCollection;
    use Illuminate\Support\Facades\Log;
    use Throwable;

    class OrderController extends Controller
    {

        public function index(): ResourceCollection
        {

            $orders = Order::query()
                ->with('items')
                ->get();

            return OrderResource::collection($orders);
        }

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

                Log::error('Order creation failed', ['error' => $e->getMessage()]);

                return response()->json([
                    'message' => 'Order could not be created'
                ], 503);

            }

            return (new OrderResource($order))
                ->response()
                ->setStatusCode(201);
        }

        public function show(Order $order): OrderResource
        {
            $order->load('items');

            return new OrderResource($order);
        }

        public function destroy(Order $order): JsonResponse
        {

            try {

                $order->cancel();

            } catch (OrderAlreadyFinalException $e) {

                return response()->json([
                    'message' => $e->getMessage()
                ], 409);
            }

            return response()->json([], 204);
        }
    }
