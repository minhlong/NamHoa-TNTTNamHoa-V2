<?php

namespace App\Http\Controllers;

use App\NhomTaiKhoan;
use App\PhanQuyen;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PhanQuyenController extends Controller
{
    public function getDanhSach()
    {
        return response()->json([
            'data' => Permission::with(['users', 'roles'])->get()->toArray(),
        ]);
    }

    public function postThongTin(PhanQuyen $phanQuyen, Request $request)
    {
        $phanQuyen->fill($request->input());
        $phanQuyen->save();

        return response()->json(true);
    }

    public function postThemNhom(PhanQuyen $phanQuyen)
    {
        $IdArr = \Request::has('IDs') ? \Request::get('IDs') : [];
        $phanQuyen->role_nhom()->attach($IdArr);
        $phanQuyen->load(['role_nhom', 'role_taikhoan']);

        return response()->json([
            'data' => $phanQuyen,
        ]);
    }

    public function postThemTaiKhoan(PhanQuyen $phanQuyen)
    {
        $nhomTaiKhoan = [];
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
        $phanQuyen->role_nhom()->attach($nhomTaiKhoan);
        $phanQuyen->load(['role_nhom', 'role_taikhoan']);

        return response()->json([
            'data' => $phanQuyen,
        ]);
    }

    public function postXoa(PhanQuyen $phanQuyen)
    {
        $IdArr = \Request::has('IDs') ? \Request::get('IDs') : [];
        $phanQuyen->role_nhom()->detach($IdArr);
        $phanQuyen->load(['role_nhom', 'role_taikhoan']);

        return response()->json([
            'data' => $phanQuyen,
        ]);
    }
}
