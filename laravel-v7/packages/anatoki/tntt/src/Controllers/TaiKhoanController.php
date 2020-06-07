<?php

namespace TNTT\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;
use TNTT\Models\TaiKhoan;
use TNTT\Repositories\TaiKhoanRepository;
use TNTT\Services\Excel\Exports\TaiKhoanExport;
use TNTT\Services\Excel\Exports\TaiKhoanInsert;
use TNTT\Services\Excel\Imports\TaiKhoanImport;
use TNTT\Services\Library;

class TaiKhoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->only([
            'getThongTin',
            'postUpdate',
            'postMatKhau',
            'postXoa',
        ]);
        $this->middleware('check-owner')->only([
            'postUpdate',
            'postMatKhau',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'postThemSuc',
            'postRuocLe',
        ]);
        $this->middleware(['can:Tài Khoản'])->only([
            'postXoa',
            'postTapTin',
            'postTao',
            'postTaoDownload',
        ]);
    }

    /**
     * @param  TaiKhoan  $taiKhoan
     * @param  Request  $request
     * @return Response
     */
    public function getDanhSach(TaiKhoan $taiKhoan, Request $request)
    {
        $taiKhoan = TaiKhoan::withTrashed()->locDuLieu()->get();

        // Thư mời - tạo mới - Tìm kiếm thông tin nên hiển thị luôn lớp học
        if ($request->has('loadLopHoc') && $request->has('khoa')) {
            $taiKhoan->load([
                'lop_hoc' => function ($query) use ($request) {
                    $query->where('khoa_hoc_id', $request->khoa);
                },
            ])->map(function ($c) {
                $c['lop_hoc']->map(function ($d) {
                    $d->ten = $d->taoTen(true);
                    return $d;
                });
                return $c;
            });
        }

        return response()->json([
            'data' => $taiKhoan,
        ]);
    }

    /**
     * Lấy Thông Tin Cá Nhân.
     * @param  TaiKhoan  $taiKhoan
     * @return JsonResponse
     */
    public function getThongTin(TaiKhoan $taiKhoan)
    {
        $taiKhoan->load(['lop_hoc']);
        foreach ($taiKhoan->lop_hoc as &$item) {
            $item->load(['huynh_truong']);
            $item->ten_lop = $item->taoTen();
        }

        return response()->json($taiKhoan->toArray());
    }

    public function generateExcelFile(TaiKhoan $taiKhoan, LopHoc $lopHoc, Request $request, Library $library)
    {
        return new TaiKhoanExport();
    }

    public function postThemSuc(Request $request)
    {
        try {
            TaiKhoan::whereIn('id', $request->get('tai_khoan'))->update(['ngay_them_suc' => $request->get('ngay_them_suc')]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Sai định dạng ngày',
            ], 400);
        }

        return response()->json(['result' => 'true']);
    }

    public function postRuocLe(Request $request)
    {
        try {
            TaiKhoan::whereIn('id', $request->get('tai_khoan'))->update(['ngay_ruoc_le' => $request->get('ngay_ruoc_le')]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Sai định dạng ngày',
            ], 400);
        }

        return response()->json();
    }

    /**
     * @param  Request  $request
     * @param  TaiKhoan  $taiKhoan
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function postUpdate(Request $request, TaiKhoan $taiKhoan)
    {
        $request->validate([
            'ngay_sinh'     => 'required|date_format:Y-m-d',
            'ngay_rua_toi'  => 'nullable|date_format:Y-m-d',
            'ngay_ruoc_le'  => 'nullable|date_format:Y-m-d',
            'ngay_them_suc' => 'nullable|date_format:Y-m-d',
            'ho_va_ten'     => 'required',
        ]);

        $taiKhoan->fill($request->all())->save();

        // Update Trang Thai
        if ($taiKhoan->trang_thai == 'TAM_NGUNG' && !$taiKhoan->trashed()) {
            $taiKhoan->delete();
        } elseif ($taiKhoan->trang_thai == 'HOAT_DONG' && $taiKhoan->trashed()) {
            $taiKhoan->restore();
        }

        return $this->getThongTin($taiKhoan);
    }

    public function postMatKhau(Request $request, TaiKhoan $taiKhoan)
    {
        $request->validate([
            'mat_khau' => 'required',
        ]);
        $taiKhoan->capNhatMatKhau($request->get('mat_khau'));
        $taiKhoan->save();

        return response()->json($taiKhoan);
    }

    public function postXoa(TaiKhoan $taiKhoan)
    {
        try {
            $taiKhoan->forceDelete();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Liên hệ quản trị',
            ], 400);
        }

        // TODO: delete relative items

        return response()->json(['result' => 'true']);
    }

    public function postTapTin(Request $request, Library $library)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $importer = new TaiKhoanImport();
        Excel::import($importer, $request->file('file'));

        return response()->json([
            'data' => $importer->getResult(),
        ]);
    }

    public function postTao(Request $request, Library $library, TaiKhoanRepository $taiKhoanRepo)
    {
        $request->validate([
            'data' => 'required',
        ]);

        $result = new Collection();
        $rows   = $request->data;
        $khoaId = KhoaHoc::hienTai()->id;
        $lops   = LopHoc::where('khoa_hoc_id', $khoaId)->get();

        DB::beginTransaction();
        foreach ($rows as $row) {
            $taiKhoan = TaiKhoan::taoTaiKhoan($row);
            if (isset($row['lop_hoc_id'])) {
                $tmpLop = $lops->filter(function ($lh) use ($row) {
                    return $lh->id == $row['lop_hoc_id'];
                })->first();

                if ($tmpLop) {
                    $taiKhoanRepo->themThanhVien($tmpLop, [$taiKhoan->id]);
                    $taiKhoan['lop_hoc_ten'] = $tmpLop->taoTen();
                }
            }
            $result->push($taiKhoan);
        }

        DB::commit();

        return response()->json([
            'data' => $result,
        ]);
    }

    public function postTaoDownload(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        return new TaiKhoanInsert($request->data);
    }
}
