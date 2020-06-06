<?php

namespace TNTT\Controllers;

use TNTT\Requests\KhoaHocRequest;
use TNTT\Models\KhoaHoc;
use Illuminate\Http\JsonResponse;

class KhoaHocController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getDanhSach()
    {
        return response()->json([
            'data' => KhoaHoc::all(),
        ]);
    }

    public function postTaoMoi()
    {
        $khoaHoc = KhoaHoc::hienTai(date('Y-m-d', strtotime('+1 year')));

        return response()->json($khoaHoc);
    }

    /**
     * Lấy thông tin chi tiết
     * @param  KhoaHoc  $khoaHoc
     * @return JsonResponse
     */
    public function getThongTin(KhoaHoc $khoaHoc)
    {
        return response()->json([
            'data' => $khoaHoc,
        ]);
    }

    /**
     * Cập nhật thông tin
     * @param  KhoaHoc  $khoaHoc
     * @param  KhoaHocRequest  $khoaHocForm
     * @return JsonResponse
     */
    public function postThongTin(KhoaHoc $khoaHoc, KhoaHocRequest $khoaHocForm)
    {
        // TODO: Chỉ cho cap nhat Năm hiện tại hoặc về sau
        $khoaHoc->fill($khoaHocForm->input());
        $khoaHoc->save();

        return response()->json($khoaHoc);
    }
}
