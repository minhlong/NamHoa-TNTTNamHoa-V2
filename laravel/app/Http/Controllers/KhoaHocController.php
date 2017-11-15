<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\KhoaHoc;

class KhoaHocController extends Controller
{
    public function getDanhSach()
    {
        return response()->json([
            'data' => KhoaHoc::all(),
        ]);
    }

    public function getKhoaHocHienTai()
    {
        return response()->json(KhoaHoc::hienTaiHoacTaoMoi());
    }


    /**
      * Lấy thông tin chi tiết
      */
    public function getThongTin(KhoaHoc $khoaHoc)
    {
        return response()->json([
            'data' => $khoaHoc
        ]);
    }

    /**
      * Cập nhật thông tin
      */
    public function postThongTin(KhoaHoc $khoaHoc, Requests\KhoaHocFormRequest $khoaHocFormRequest)
    {
        $khoaHoc->fill($khoaHocFormRequest->input());
        $khoaHoc->save();

        return response()->json($khoaHoc);
    }

    public function postTaoMoi()
    {
        $khoaHoc = KhoaHoc::hienTaiHoacTaoMoi(date('Y-m-d', strtotime('+1 year')));

        return response()->json($khoaHoc);
    }
}
