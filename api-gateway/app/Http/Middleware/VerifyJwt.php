<?php

    namespace App\Http\Middleware;

    use Closure;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class VerifyJwt
    {
        public function handle($request, Closure $next)
        {
            $header = $request->header('Authorization');

            if (!$header || !str_starts_with($header, 'Bearer ')) {
                return response()->json(['error' => 'Missing token'], 401);
            }

            $token = substr($header, 7);

            try {
                $payload = JWT::decode(
                    $token,
                    new Key(config('services.jwt.secret'), 'HS256')
                );

                // přidáme user_id do requestu
                $request->headers->set('X-User-Id', $payload->sub);

            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            return $next($request);
        }
    }
