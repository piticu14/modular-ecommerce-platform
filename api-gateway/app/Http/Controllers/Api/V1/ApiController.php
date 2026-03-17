<?php

    namespace App\Http\Controllers\Api\V1;

    use App\Http\Controllers\Controller;
    use App\Services\Proxy\ServiceProxy;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    abstract class ApiController extends Controller
    {
        public function __construct(
            protected readonly ServiceProxy $proxy
        ) {}

        protected function forwardToService(Request $request, string $service): Response
        {
            $config = config("services.proxy.services.$service");

            if (!$config) {
                abort(404, "Service [$service] is not configured.");
            }

            $response = $this->proxy->forward(
                request: $request,
                service: $service
            );

            return response($response->body(), $response->status())
                ->withHeaders($response->headers());
        }
    }
