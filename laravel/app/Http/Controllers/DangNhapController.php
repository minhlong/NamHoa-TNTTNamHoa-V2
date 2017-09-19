<?php
namespace App\Http\Controllers;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\KhoaHoc;

class DangNhapController extends Controller
{
    public function postDangNhap(Request $request)
    {
        $credentials = $request->only('id', 'password');
        try {
            if (!\Auth::attempt($credentials)) {
            	return response()->json('Thông tin đăng nhập không đúng!', 401);
            }

            if (\Auth::user()->loai_tai_khoan == 'THIEU_NHI') {
            	return response()->json('Tài khoản không có quyền!', 401);
            }
        } catch (JWTException $e) {
        	return response()->json('Liên hệ Admin!', 401);
        }

        // $result = array_merge(\Auth::user()->toArray(), \Auth::user()->getPhanQuyen());
        $lopHoc = \Auth::user()->lop_hoc()->where('khoa_hoc_id', KhoaHoc::hienTaiHoacTaoMoi()->id)->first();

        return response()->json([
            'data' => JWTAuth::fromUser(\Auth::user(), [
                'tai_khoan' => \Auth::user(),
                'lop_hoc_id' => $lopHoc ? $lopHoc->id : null,
            ]),
        ]);
    }
}
