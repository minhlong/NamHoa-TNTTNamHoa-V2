<?php

namespace TNTT\Controllers;

use TNTT\Services\Excel\Exports\TongKetSheet;
use TNTT\LopHoc;
use Illuminate\Http\JsonResponse as JsonResponseAlias;

class LopHocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Lay danh sach lop hoc cua 1 Khoa Hoc
     * @param $khoaHocID
     * @param  LopHoc  $lopHoc
     * @return JsonResponseAlias
     */
    public function getDanhSachTheoKhoa($khoaHocID, LopHoc $lopHoc)
    {
        $lopHoc = $lopHoc->where('khoa_hoc_id', $khoaHocID)
            ->locDuLieu()
            ->get()
            ->load('huynh_truong')
            ->map(function ($c) {
                $c['ten'] = $c->taoTen();
                return $c;
            });

        return response()->json([
            'data' => $lopHoc,
        ]);
    }

    /**
     * Lay thong tin chi tiet cua 1 lop hoc
     * @param  LopHoc  $lopHoc
     * @return JsonResponseAlias
     */
    public function getThongTin(LopHoc $lopHoc)
    {
        $lopHoc->load(['huynh_truong', 'hoc_vien']);
        $lopHoc->ten = $lopHoc->taoTen();

        return response()->json($lopHoc);
    }

    public function getTongKet(LopHoc $lopHoc)
    {
        return response()->json(
            (new TongKetSheet($lopHoc))->getData()
        );
    }
}
