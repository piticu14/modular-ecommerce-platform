<?php


    namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Hash;

    class AuthController extends Controller
    {

        /**
         * Create a new user
         *
         * @return \Illuminate\Http\JsonResponse
         */

        public function register(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password']
            ]);

            return response()->json([
                'message' => 'User created'
            ], 201);
        }

        /**
         * Get a JWT via given credentials.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function login(Request $request)
        {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);


            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);
        }

        /**
         * Get the authenticated User.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function me()
        {
            return response()->json(auth()->user());
        }

        /**
         * Log the user out (Invalidate the token).
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function logout()
        {
            auth()->logout();

            return response()->json(['message' => 'Successfully logged out']);
        }

        /**
         * Refresh a token.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function refresh()
        {
            return $this->respondWithToken(auth()->refresh());
        }

        /**
         * Get the token array structure.
         *
         * @param  string $token
         *
         * @return \Illuminate\Http\JsonResponse
         */
        protected function respondWithToken($token)
        {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
        }
    }
