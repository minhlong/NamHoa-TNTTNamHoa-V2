<?php

namespace TNTT\Controllers;

use Auth;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

class DangNhapController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['dangNhap']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function dangNhap()
    {
        $credentials = request(['id', 'password']);

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Thông tin đăng nhập không đúng!'], 401);
            }

            if (Auth::user()->loai_tai_khoan == 'THIEU_NHI') {
                return response()->json(['error' => 'Tài khoản không có quyền!'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Liên hệ Admin!'], 500);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'data'       => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function dangXuat()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
