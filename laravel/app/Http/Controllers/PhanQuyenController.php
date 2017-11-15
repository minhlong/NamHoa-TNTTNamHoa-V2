<?php
namespace App\Http\Controllers;

use App\NhomTaiKhoan;
use App\PhanQuyen;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    public function getDanhSach()
    {
        return response()->json([
            'data' => PhanQuyen::with('role_nhom')
                ->with('role_taikhoan')
                ->get(),
        ]);
    }

    public function postThongTin(PhanQuyen $phanQuyen, Request $request)
    {
        $phanQuyen->fill($request->input());
        $phanQuyen->save();

        return response()->json($phanQuyen);
    }

    public function postThemNhom(PhanQuyen $phanQuyen)
    {
        $nhomTaiKhoan = array_keys(array_filter(\Request::get('NhomTaiKhoan')));
        foreach (\Request::get('TaiKhoan') as $taiKhoan) {
            $newRoles = NhomTaiKhoan::firstOrCreate([
                'loai' => 'TAI_KHOAN',
                'ten'  => $taiKhoan['id'],
            ]);
            $newRoles->ten_hien_thi = $taiKhoan['ho_va_ten'];
            $newRoles->save();
            $newRoles->tai_khoan()->sync([$taiKhoan['id']]);
            $nhomTaiKhoan[] = $newRoles->id;
        }
        $phanQuyen->role_nhom()->sync($nhomTaiKhoan);
        $phanQuyen->load(['role_nhom', 'role_taikhoan']);

        return response()->json($phanQuyen);
    }
}
