<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResponse
     */
    public function login()
    {

        \DB::listen(function($sql) {
            var_dump($sql);
        });
        $credentials = request(['id', 'password']);
        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Thông tin đăng nhập không đúng!'], 401);
            }

//        if (\Auth::user()->loai_tai_khoan == 'THIEU_NHI') {
//            return response()->json(['error' => 'Tài khoản không có quyền!'], 401);
//        }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Liên hệ Admin!'], 500);
        }
        return $this->respondWithToken($token);

//        $khoaHoc = KhoaHoc::hienTaiHoacTaoMoi();
//        $lopHoc  = Auth::user()->lop_hoc()->where('khoa_hoc_id', $khoaHoc->id)->first();
//
//        return response()->json([
//            'data' => JWTAuth::fromUser(Auth::user(), [
//                'tai_khoan'            => Auth::user(),
//                'phan_quyen'           => Auth::user()->getPhanQuyen(),
//                'lop_hoc_hien_tai_id'  => $lopHoc ? $lopHoc->id : null,
//                'khoa_hoc_hien_tai_id' => $khoaHoc ? $khoaHoc->id : null,
//            ]),
//        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
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

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
