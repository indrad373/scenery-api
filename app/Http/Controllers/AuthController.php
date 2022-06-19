<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    //buat AuthController instance yang baru
    public function __construct()
    {
        $this->middleware('auth.api', ['except' => ['login']]);
        return auth()->shouldUse('api');
    }

    //get JWT via given credentials
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)){
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        return $this->respondWithToken($token);
    }

    //get authenticated user
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    //logout the user, buat token nya invalid
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // auth()->logout();

        // return response()->json(['message' => 'Successfully logged out']);
    }

    //refresh sebuah token
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        //true pertama itu force blacklist token yang sebelumnya, dan yang kedua generate token yang baru.
        return $this->respondWithToken(auth()->refresh(true, true));
    }

    /**
     * Get token array structure
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'code' => 200,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'data' => auth()->user()
        ]);
    }
}
