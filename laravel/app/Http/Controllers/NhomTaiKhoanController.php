<?php
namespace App\Http\Controllers;

use App\NhomTaiKhoan;

class NhomTaiKhoanController extends Controller
{
    public function get()
    {
        return response()->json(
            NhomTaiKhoan::whereLoai('NHOM')
                ->with('tai_khoan')
                ->get()
        );
    }

    public function post(NhomTaiKhoan $nhomTaiKhoan)
    {
        $nhomTaiKhoan->fill(\Request::all());
        $nhomTaiKhoan->loai = 'NHOM';
        $nhomTaiKhoan->ten = $nhomTaiKhoan->ten_hien_thi;
        $nhomTaiKhoan->save();
        $arrTaiKhoanID = array_pluck(\Request::get('TaiKhoan'), 'id');
        $nhomTaiKhoan->tai_khoan()->sync($arrTaiKhoanID);
        $nhomTaiKhoan->load(['tai_khoan']);

        return response()->json($nhomTaiKhoan);
    }

    public function delete(NhomTaiKhoan $nhomTaiKhoan)
    {
        $nhomTaiKhoan->delete();

        return response()->json(true);
    }
}
