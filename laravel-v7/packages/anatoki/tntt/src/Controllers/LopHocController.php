<?php

namespace TNTT\Controllers;

use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use TNTT\Models\DiemDanh;
use TNTT\Models\DiemSo;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;
use TNTT\Repositories\TaiKhoanRepository;
use TNTT\Services\Excel\Exports\LopHocInserted;
use TNTT\Services\Excel\Exports\TongKetSheet;
use TNTT\Services\Excel\Imports\LopHocImport;

class LopHocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->except([
            'index',
            'importStep1',
            'importStep2',
            'importStep3',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'update',
            'delete',
            'postMember',
            'deleteMember',
            'importStep1',
            'importStep2',
            'importStep3',
        ]);
        $this->middleware(['can:Tổng Kết Cuối Năm'])->only([
            'postXepHang',
        ]);
        $this->middleware('isTeacher')->only([
            'postChuyenCan',
            'postHocLuc',
            'postNhanXet',
        ]);
    }

    /**
     * Lay danh sach lop hoc cua 1 Khoa Hoc
     * @param $khoaHocID
     * @param  LopHoc  $lopHoc
     * @return JsonResponse
     */
    public function index($khoaHocID, LopHoc $lopHoc)
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
     * @return JsonResponse
     */
    public function show(LopHoc $lopHoc)
    {
        $lopHoc->load(['huynh_truong', 'hoc_vien']);
        $lopHoc->ten = $lopHoc->taoTen();

        return response()->json($lopHoc);
    }

    /**
     * Update file
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function importStep1(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $importer = new LopHocImport();
        Excel::import($importer, $request->file('file'));

        return response()->json([
            'data' => $importer->getResult(),
        ]);
    }

    public function importStep2(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        $result = new Collection();
        $rows   = $request->data;
        $khoaId = KhoaHoc::hienTai()->id;

        DB::beginTransaction();
        foreach ($rows as $row) {
            try {
                /** @var LopHoc $lopHoc */
                $lopHoc = LopHoc::create(array_merge(['khoa_hoc_id' => $khoaId,], $row));
            } catch (Exception $e) {
                continue;
            }

            $lopHoc->ten = $lopHoc->taoTen();
            $result->push($lopHoc);
        }
        DB::commit();

        return response()->json([
            'data' => $result,
        ]);
    }

    /**
     * Download imported user
     *
     * @param  Request  $request
     * @return LopHocInserted
     */
    public function importStep3(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        return new LopHocInserted($request->data);
    }

    /**
     * @param  LopHoc  $lopHoc
     * @return JsonResponse
     */
    public function update(LopHoc $lopHoc, Request $request)
    {
        try {
            $lopHoc->fill($request->all());
            $lopHoc->save();
        } catch (Exception $e) {
            abort(400, 'Kiểm tra lại thông tin.');
        }

        return $this->show($lopHoc);
    }

    public function delete(LopHoc $lopHoc)
    {
        $lopHoc->delete();
        return response()->json(['result' => true]);
    }

    public function getTongKet(LopHoc $lopHoc)
    {
        return response()->json(
            (new TongKetSheet($lopHoc))->getData()
        );
    }

    /**
     * @param  LopHoc  $lopHoc
     * @param  Request  $request
     * @param  TaiKhoanRepository  $taiKhoanRepo
     * @return JsonResponse
     */
    public function postMember(LopHoc $lopHoc, Request $request, TaiKhoanRepository $taiKhoanRepo)
    {
        $request->validate([
            'id' => 'required|array',
        ]);

        try {
            $taiKhoanRepo->themThanhVien($lopHoc, $request->id);
        } catch (Exception $e) {
            abort(400, 'Kiểm tra lại thông tin.');
        }

        return $this->show($lopHoc);
    }

    /**
     * @param  LopHoc  $lopHoc
     * @param  Request  $request
     * @return JsonResponse
     */
    public function deleteMember(LopHoc $lopHoc, Request $request)
    {
        $request->validate([
            'id' => 'required|array',
        ]);

        $lopHoc->thanh_vien()->detach($request->id);
        $lopHoc->tinhTongKet();

        return $this->show($lopHoc);
    }


    /**
     * @param  LopHoc  $lopHoc
     * @param  DiemDanh  $diemDanh
     * @return JsonResponse
     */
    public function getChuyenCan(LopHoc $lopHoc, DiemDanh $diemDanh, Request $request)
    {
        $request->validate([
            'ngay_hoc' => 'required|date_format:Y-m-d',
        ]);

        $arrHocVien = $lopHoc->hoc_vien()->pluck('tai_khoan_id')->toArray();
        $sDate      = $this->getSunday($lopHoc, $request->ngay_hoc);

        return response()->json([
            'data'   => $diemDanh->getChuyenCanData($arrHocVien, $sDate),
            'sunday' => $sDate,
        ]);
    }

    /**
     * @param  LopHoc  $lopHoc
     * @param $ngay_hoc
     * @return mixed|null
     */
    private function getSunday(LopHoc $lopHoc, $ngay_hoc)
    {
        // Trong pham vi 6 ngay
        $endDate   = strtotime($ngay_hoc);
        $startDate = strtotime('-6day', $endDate);

        // Chỉ hiện ngày trong phạm vi của Khóa Học Tương Ứng
        if ($startDate < strtotime($lopHoc->khoa_hoc->ngay_bat_dau)) {
            $startDate = strtotime($lopHoc->khoa_hoc->ngay_bat_dau);
        }
        if ($endDate > strtotime($lopHoc->khoa_hoc->ngay_ket_thuc)) {
            $endDate = strtotime($lopHoc->khoa_hoc->ngay_ket_thuc);
        }
        $startDate = date('Y-m-d', $startDate);
        $endDate   = date('Y-m-d', $endDate);

        // Lay ngay Chua Nhat
        $aDate = getSundays($startDate, $endDate);

        if (empty($aDate)) {
            abort(400, 'Ngày không hợp lệ.');
        }

        return array_shift($aDate);
    }

    private function isSunday($date)
    {
        return date('w', strtotime($date)) == 0;
    }

    public function postChuyenCan(LopHoc $lopHoc, Request $request, DiemDanh $diemDanh)
    {
        $request->validate([
            'thieu_nhi' => 'required|array',
            'ngay'      => 'required|date_format:Y-m-d',
        ]);

        if (!$this->isSunday($request->ngay)) {
            abort(400, 'Ngày chọn không phải là ngày Chúa Nhật.');
        }

        $diemDanh->luuChuyenCan($lopHoc, $request->thieu_nhi, $request->ngay);

        return response()->json($request);
    }

    public function getHocLuc(LopHoc $lopHoc, Request $request, DiemSo $diemSo)
    {
        $request->validate([
            'dot' => 'required',
        ]);

        $arrHocVien = $lopHoc->hoc_vien()->pluck('tai_khoan_id')->toArray();

        return response()->json([
            'data' => $diemSo->getHocLuc($arrHocVien, $lopHoc->khoa_hoc, $request->dot),
            'dot'  => $request->dot,
        ]);
    }

    public function postHocLuc(LopHoc $lopHoc, Request $request, DiemSo $diemSo)
    {
        $request->validate([
            'thieu_nhi' => 'required|array',
            'dot'       => 'required',
            'lan'       => 'required',
        ]);

        $diemSo->luuHocLuc($lopHoc, $request->thieu_nhi, $request->dot, $request->lan);

        return response()->json(['result' => true]);
    }

    public function postXepHang(LopHoc $lopHoc, Request $request)
    {
        $request->validate([
            'thieu_nhi' => 'required|array',
        ]);

        foreach ($request->thieu_nhi as $arrTmp) {
            $hocVien = $lopHoc->hoc_vien()
                ->where('tai_khoan_id', $arrTmp['id'])
                ->first();

            $hocVien->pivot->xep_hang = $arrTmp['xep_hang'];
            $hocVien->pivot->ghi_chu  = $arrTmp['ghi_chu'];
            $hocVien->pivot->save();
        }

        return response()->json(['result' => true]);
    }

    public function postNhanXet(LopHoc $lopHoc, Request $request)
    {
        $request->validate([
            'thieu_nhi' => 'required|array',
        ]);

        foreach ($request->thieu_nhi as $arrTmp) {
            $hocVien = $lopHoc->hoc_vien()
                ->where('tai_khoan_id', $arrTmp['id'])
                ->first();

            $hocVien->pivot->nhan_xet = $arrTmp['nhan_xet'];
            $hocVien->pivot->save();
        }

        return response()->json(['result' => true]);
    }
}
