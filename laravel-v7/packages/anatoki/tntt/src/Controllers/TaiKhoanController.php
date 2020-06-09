<?php

namespace TNTT\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;
use TNTT\Models\TaiKhoan;
use TNTT\Repositories\TaiKhoanRepository;
use TNTT\Services\Excel\Exports\TaiKhoanExport;
use TNTT\Services\Excel\Exports\TaiKhoanInserted;
use TNTT\Services\Excel\Imports\TaiKhoanImport;

class TaiKhoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->only([
            'show',
            'update',
            'updatePassword',
            'delete',
        ]);
        $this->middleware('isOwner')->only([
            'update',
            'updatePassword',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'updateThemSuc',
            'updateRuocLe',
        ]);
        $this->middleware(['can:Tài Khoản'])->only([
            'delete',
            'importStep1',
            'importStep2',
            'importStep3',
        ]);
    }

    /**
     * @param  TaiKhoan  $taiKhoan
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
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
    public function show(TaiKhoan $taiKhoan)
    {
        $taiKhoan->load(['lop_hoc']);
        foreach ($taiKhoan->lop_hoc as &$item) {
            $item->load(['huynh_truong']);
            $item->ten_lop = $item->taoTen();
        }

        return response()->json($taiKhoan->toArray());
    }

    public function export()
    {
        return new TaiKhoanExport();
    }

    public function updateThemSuc(Request $request)
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

    public function updateRuocLe(Request $request)
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
    public function update(Request $request, TaiKhoan $taiKhoan)
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

        return $this->show($taiKhoan);
    }

    public function updatePassword(Request $request, TaiKhoan $taiKhoan)
    {
        $request->validate([
            'mat_khau' => 'required',
        ]);
        $taiKhoan->capNhatMatKhau($request->get('mat_khau'));
        $taiKhoan->save();

        return response()->json($taiKhoan);
    }

    public function delete(TaiKhoan $taiKhoan)
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

    /**
     * Update excel file
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function importStep1(Request $request)
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

    /**
     * Insert into DB
     *
     * @param  Request  $request
     * @param  TaiKhoanRepository  $taiKhoanRepo
     * @return JsonResponse
     * @throws Throwable
     */
    public function importStep2(Request $request, TaiKhoanRepository $taiKhoanRepo)
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

    /**
     * Download imported user
     *
     * @param  Request  $request
     * @return TaiKhoanInserted
     */
    public function importStep3(Request $request)
    {
        $request->validate([
            'data' => 'required',
        ]);

        return new TaiKhoanInserted($request->data);
    }
}
