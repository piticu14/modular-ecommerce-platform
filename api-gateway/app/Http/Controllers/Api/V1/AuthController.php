<?php

    namespace App\Http\Controllers\Api\V1;

    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class AuthController extends ApiController
    {
        /**
         * Login
         *
         * Authenticates user and returns JWT token.
         *
         * @group Authentication
         *
         * @bodyParam email string required User email. Example: user@example.com
         * @bodyParam password string required User password. Example: secret123
         *
         * @response 200 {
         *   "access_token": "jwt_token_here",
         *   "token_type": "bearer",
         *   "expires_in": 3600
         * }
         *
         * @response 401 {
         *   "error": "Unauthorized"
         * }
         */
        public function login(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Register
         *
         * Registers a new user.
         *
         * @group Authentication
         *
         * @bodyParam name string required Full name. Example: John Doe
         * @bodyParam email string required User email. Example: user@example.com
         * @bodyParam password string required Password (min 6 characters). Example: secret123
         *
         * @response 201 {
         *   "message": "User created"
         * }
         *
         * @response 422 {
         *   "message": "The email has already been taken.",
         *   "errors": {
         *     "name": ["The name field is required."],
         *     "email": ["The email has already been taken."],
         *     "password": ["The password must be at least 6 characters."]
         *   }
         * }
         */
        public function register(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Get current user
         *
         * Returns authenticated user information.
         *
         * @group Authentication
         * @authenticated
         *
         * @response 200 {
         *   "id": 1,
         *   "name": "John Doe",
         *   "email": "user@example.com",
         *   "created_at": "2023-10-27T12:00:00.000000Z",
         *   "updated_at": "2023-10-27T12:00:00.000000Z"
         * }
         *
         * @response 401 {
         *   "message": "Unauthenticated."
         * }
         */
        public function me(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }

        /**
         * Refresh token
         *
         * Refreshes JWT token.
         *
         * @group Authentication
         * @authenticated
         *
         * @response 200 {
         *   "access_token": "new_jwt_token",
         *   "token_type": "bearer",
         *   "expires_in": 3600
         * }
         */
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
         * @authenticated
         *
         * @response 200 {
         *   "message": "Successfully logged out"
         * }
         */
        public function logout(Request $request): Response
        {
            return $this->forwardToService($request, 'auth');
        }
    }
