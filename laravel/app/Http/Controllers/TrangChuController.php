<?php
namespace App\Http\Controllers;

use App\DiemDanh;
use App\KhoaHoc;
use App\LopHoc;
use App\Services\Library;
use App\TaiKhoan;
use Illuminate\Http\Request;

class TrangChuController extends Controller
{
    public function getThongTin(Library $library)
    {
        $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
        $counterAuNhi = TaiKhoan::whereHas('lop_hoc', function ($query) use ($khoaHocID) {
            $query->where('khoa_hoc_id', $khoaHocID)->where('nganh', 'AU_NHI');
        })->where('loai_tai_khoan', 'THIEU_NHI')->get()->count();
        $counterThieuNhi = TaiKhoan::whereHas('lop_hoc', function ($query) use ($khoaHocID) {
            $query->where('khoa_hoc_id', $khoaHocID)->where('nganh', 'THIEU_NHI');
        })->where('loai_tai_khoan', 'THIEU_NHI')->get()->count();
        $countNghiaSi = TaiKhoan::whereHas('lop_hoc', function ($query) use ($khoaHocID) {
            $query->where('khoa_hoc_id', $khoaHocID)->where('nganh', 'NGHIA_SI');
        })->where('loai_tai_khoan', 'THIEU_NHI')->get()->count();
        $countHTDuBi = TaiKhoan::whereHas('lop_hoc', function ($query) use ($khoaHocID) {
            $query->where('khoa_hoc_id', $khoaHocID)->where('nganh', 'HT_DU_BI');
        })->where('loai_tai_khoan', 'THIEU_NHI')->get()->count();
        $countHT = TaiKhoan::where('loai_tai_khoan', 'HUYNH_TRUONG')->whereHas('lop_hoc', function ($query) use ($khoaHocID) {
            $query->where('khoa_hoc_id', $khoaHocID);
        })->get()->count();

        return response()->json([
            'si_so'          => [
                'au_nhi'       => $counterAuNhi,
                'thieu_nhi'    => $counterThieuNhi,
                'nghia_si'     => $countNghiaSi,
                'ht_du_bi'     => $countHTDuBi,
                'huynh_truong' => $countHT,
            ],
            'chua_diem_danh' => $this->getChuaDiemDanh($library),
        ]);
    }

    protected function getChuaDiemDanh(Library $library)
    {
        // Trong pham vi 6 ngay
        $endDate = strtotime('now');
        $startDate = strtotime('-6day', $endDate);
        $startDate = date('Y-m-d', $startDate);
        $endDate = date('Y-m-d', $endDate);
        // Lay ngay Chua Nhat
        $currentSunday = $library->SpecificDayBetweenDates($startDate, $endDate);
        $currentSunday = array_shift($currentSunday);
        $result = [];
        $list = LopHoc::where('khoa_hoc_id', KhoaHoc::hienTaiHoacTaoMoi()->id)
            ->orderBy('nganh')
            ->orderBy('cap')
            ->orderBy('doi')
            ->get()->load('huynh_truong');
        foreach ($list as $obj) {
            $id = $obj->hoc_vien()->pluck('tai_khoan.id');
            $counter = DiemDanh::where('ngay', $currentSunday)->whereIn('tai_khoan_id', $id)->get()->count();
            if (!$counter && !DiemDanh::checkCacheReport($currentSunday, $obj->id)) {
                $result[] = [
                    'id'  => $obj->id,
                    'ten' => $obj->taoTen(true),
                    'huynh_truong' => $obj->huynh_truong,
                ];
            }
        }

        return [
            'ngay' => $currentSunday,
            'lop'  => $result,
        ];
    }
}
