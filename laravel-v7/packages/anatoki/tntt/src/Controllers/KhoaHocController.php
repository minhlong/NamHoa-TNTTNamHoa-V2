<?php

namespace TNTT\Controllers;

use Illuminate\Http\JsonResponse;
use TNTT\Models\KhoaHoc;
use TNTT\Requests\KhoaHocRequest;

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
        $this->middleware(['bindings'])->only([
            'show',
            'update',
        ]);
        $this->middleware(['can:Hệ Thống'])->only([
            'store',
            'update',
        ]);
    }

    public function index()
    {
        return response()->json([
            'data' => KhoaHoc::all(),
        ]);
    }

    public function store()
    {
        $khoaHoc = KhoaHoc::hienTai(date('Y-m-d', strtotime('+1 year')));

        return response()->json($khoaHoc);
    }

    /**
     * Lấy thông tin chi tiết
     * @param  KhoaHoc  $khoa_hoc
     * @return JsonResponse
     */
    public function show(KhoaHoc $khoa_hoc)
    {
        return response()->json([
            'data' => $khoa_hoc,
        ]);
    }

    /**
     * Cập nhật thông tin
     * @param  KhoaHoc  $khoa_hoc
     * @param  KhoaHocRequest  $khoaHocForm
     * @return JsonResponse
     */
    public function update(KhoaHoc $khoa_hoc, KhoaHocRequest $khoaHocForm)
    {
        // TODO: Chỉ cho cap nhat Năm hiện tại hoặc về sau
        $khoa_hoc->fill($khoaHocForm->input());
        $khoa_hoc->save();

        return response()->json($khoa_hoc);
    }
}
