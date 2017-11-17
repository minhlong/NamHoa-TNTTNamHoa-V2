<?php
namespace App\Http\Controllers;

use App\Services\Library;
use App\ThuMoi;
use App\KhoaHoc;

class ThuMoiController extends Controller
{
    /**
     * @param ThuMoi $thumoi
     * @param Library $library
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDanhSach(ThuMoi $thumoi, Library $library)
    {
        $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
        $thumoi = $thumoi->locDuLieu($library)
            ->with(['tai_khoan' => function($query) use ($khoaHocID) {
                return $query->with(['lop_hoc' => function($query) use ($khoaHocID) {
                    return $query->where('khoa_hoc_id', $khoaHocID);
                }]);
            }])->get()->map(function ($c) {
                $c['tai_khoan']['lop_hoc']->map(function ($d) {
                    $d->ten = $d->taoTen();
                    return $d;
                });
                return $c;
            });

        return response()->json([
            'data' => $thumoi,
        ]);
    }

    public function post(ThuMoi $thuMoi, Library $library)
    {
        $thuMoi->fill(\Request::all());
        $thuMoi->save();

        return $this->getThongTin($thuMoi, $library);
    }

    public function getThongTin(ThuMoi $thuMoi, Library $library)
    {
        return response()->json($thuMoi);
    }

    public function delete(ThuMoi $thumoi)
    {
        $thumoi->delete();

        return response()->json(true);
    }
}
