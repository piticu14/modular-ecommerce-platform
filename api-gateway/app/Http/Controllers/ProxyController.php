<?php
    namespace App\Http\Controllers;

    use App\Services\Proxy\ServiceProxy;
    use Illuminate\Http\Request;

    class ProxyController extends Controller
    {
        public function __construct(private ServiceProxy $proxy) {}

        public function forward(Request $request, string $service)
        {
            $services = config('services.proxy.services');

            $config = $services[$service] ?? null;

            if (!$config) {
                abort(404);
            }

            $response = $this->proxy->forward(
                request: $request,
                baseUrl: $config['url'],
                signed: $config['signed']
            );

            return response($response->body(), $response->status())
                ->withHeaders($response->headers());
        }
    }
