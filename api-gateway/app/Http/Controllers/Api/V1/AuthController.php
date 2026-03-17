<?php

    namespace App\Http\Controllers\Api\V1;

    use App\Http\Requests\Api\V1\Auth\LoginRequest;
    use App\Http\Requests\Api\V1\Auth\RegisterRequest;
    use App\Http\Resources\Api\V1\Auth\TokenResource;
    use App\Http\Resources\Api\V1\Auth\UserResource;
    use App\Http\Resources\Api\V1\Common\ErrorResource;
    use Dedoc\Scramble\Attributes\Response as DedocResponse;
    use Dedoc\Scramble\Attributes\Group;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class AuthController extends ApiController
    {
        /**
         * Login
         *
         * Authenticates user and returns JWT token.
         *
         * @unauthenticated
         *
        */

        #[DedocResponse(200, 'OK', type: TokenResource::class)]
        #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
        #[DedocResponse(422, 'Unprocessable Content', type: ErrorResource::class)]
        #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
        #[Group('Auth - Public')]
        public function login(LoginRequest $request)
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Register
         *
         * Registers a new user.
         *
         * @unauthenticated
         *
         */

        #[DedocResponse(201, 'Created', type: UserResource::class)]
        #[DedocResponse(422, 'Unprocessable Content', type: ErrorResource::class)]
        #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
        #[Group('Auth - Public')]
        public function register(RegisterRequest $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Get current user
         *
         * Returns authenticated user information.
         *
         * @authenticated
         *
         */

        #[DedocResponse(200, 'OK', type: UserResource::class)]
        #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
        #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
        #[Group('Auth - Private')]
        public function me(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Refresh token
         *
         * Refreshes JWT token.
         *
         * @authenticated
         *
         */

        #[DedocResponse(200, 'OK', type: TokenResource::class)]
        #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
        #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
        #[Group('Auth - Private')]
        public function refresh(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Logout
         *
         * Invalidates current user token.
         *
         * @group Authentication
         *
         */

        #[DedocResponse(200, 'OK', type: 'array{message: string}')]
        #[DedocResponse(401, 'Unauthorized', type: ErrorResource::class)]
        #[DedocResponse(503, 'Service unavailable', type: ErrorResource::class)]
        #[Group('Auth - Private')]
        public function logout(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }
    }
