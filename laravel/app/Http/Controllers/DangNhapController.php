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
            	return response()->json(['error' => 'Thông tin đăng nhập không đúng!'], 400);
            }

            if (\Auth::user()->loai_tai_khoan == 'THIEU_NHI') {
            	return response()->json(['error' => 'Tài khoản không có quyền!'], 400);
            }
        } catch (JWTException $e) {
        	return response()->json(['error' => 'Liên hệ Admin!'], 400);
        }

        $khoaHoc = KhoaHoc::hienTaiHoacTaoMoi();
        $lopHoc = \Auth::user()->lop_hoc()->where('khoa_hoc_id', $khoaHoc->id)->first();

        return response()->json([
            'data' => JWTAuth::fromUser(\Auth::user(), [
                'tai_khoan' => \Auth::user(),
                'phan_quyen' => \Auth::user()->getPhanQuyen(),
                'lop_hoc_hien_tai_id' => $lopHoc ? $lopHoc->id : null,
                'khoa_hoc_hien_tai_id' => $khoaHoc ? $khoaHoc->id : null,
            ]),
            // 'tes' => [
            //     'phan_quyen' => \Auth::user()->getPhanQuyen(),
            // ]
        ]);
    }
}
