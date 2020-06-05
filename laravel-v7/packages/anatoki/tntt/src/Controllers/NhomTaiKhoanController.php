<?php

namespace TNTT\Controllers;

use TNTT\NhomTaiKhoan;
use Spatie\Permission\Models\Role;

class NhomTaiKhoanController extends Controller
{
    public function get()
    {
        return response()->json([
            'data' => Role::with('users')->get(),
        ]);
    }

    public function post(NhomTaiKhoan $nhomTaiKhoan)
    {
        try {
            $nhomTaiKhoan->fill(\Request::all());
            $nhomTaiKhoan->loai = 'NHOM';
            $nhomTaiKhoan->ten  = $nhomTaiKhoan->ten_hien_thi;
            $nhomTaiKhoan->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Liên hệ quản trị',
            ], 400);
        }

        return response()->json([
            'data' => $nhomTaiKhoan->load('tai_khoan'),
        ]);
    }

    public function postThem(NhomTaiKhoan $nhomTaiKhoan)
    {
        $arrTaiKhoanID = \Request::has('TaiKhoan') ? \Request::get('TaiKhoan') : [];
        $nhomTaiKhoan->tai_khoan()->attach($arrTaiKhoanID);

        return response()->json([
            'data' => $nhomTaiKhoan->load('tai_khoan'),
        ]);
    }

    public function postXoa(NhomTaiKhoan $nhomTaiKhoan)
    {
        $arrTaiKhoanID = \Request::has('TaiKhoan') ? \Request::get('TaiKhoan') : [];
        $nhomTaiKhoan->tai_khoan()->detach($arrTaiKhoanID);

        return response()->json([
            'data' => $nhomTaiKhoan->load('tai_khoan'),
        ]);
    }

    public function delete(NhomTaiKhoan $nhomTaiKhoan)
    {
        $nhomTaiKhoan->delete();

        return response()->json(true);
    }
}
