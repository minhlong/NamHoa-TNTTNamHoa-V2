<?php

namespace TNTT\Controllers;

use Entrust;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TNTT\Models\LopHoc;
use TNTT\Models\TaiKhoan;
use TNTT\Requests\TaiKhoanFormRequest;
use TNTT\Services\Excel\Exports\TaiKhoanExport;
use TNTT\Services\Library;

class TaiKhoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(['bindings'])->only([
            'getThongTin',
        ]);
        $this->middleware(['can:Lớp Học'])->only([
            'postThemSuc',
            'postRuocLe',
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
     * Luu Thong Tin Tai Khoan.
     *
     * @param  TaiKhoan  $taiKhoan
     * @param  TaiKhoanFormRequest  $taiKhoanFormRequest
     *
     * @return string
     */
    // public function postUpdate(TaiKhoan $taiKhoan, TaiKhoanFormRequest $taiKhoanFormRequest)
    // {
    //     if (!Entrust::can('tai-khoan') && $taiKhoan->id != Auth::user()->id) {
    //         abort(403);
    //     }
    //
    //     $taiKhoan->fill($taiKhoanFormRequest->all());
    //     $taiKhoan->save();
    //     // Update Trang Thai
    //     if ($taiKhoan->trang_thai == 'TAM_NGUNG' && !$taiKhoan->trashed()) {
    //         $taiKhoan->delete();
    //     } elseif ($taiKhoan->trang_thai == 'HOAT_DONG' && $taiKhoan->trashed()) {
    //         $taiKhoan->restore();
    //     }
    //
    //     return $this->getThongTin($taiKhoan);
    // }

    // public function postMatKhau(TaiKhoan $taiKhoan)
    // {
    //     if (!Entrust::can('tai-khoan') && $taiKhoan->id != Auth::user()->id) {
    //         abort(403);
    //     }
    //
    //     $taiKhoan->capNhatMatKhau(\Request::get('mat_khau'));
    //     $taiKhoan->save();
    //
    //     return response()->json($taiKhoan);
    // }

    // public function postXoa(TaiKhoan $taiKhoan)
    // {
    //     try {
    //         $taiKhoan->forceDelete();
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => 'Liên hệ quản trị',
    //         ], 400);
    //     }
    //
    //     return response()->json();
    // }

    // public function postTapTin(Request $request, Library $library)
    // {
    //     if (!$request->hasFile('file')) {
    //         return response()->json([
    //             'error' => 'Không tìm thấy tập tin.',
    //         ], 400);
    //     }
    //
    //     $file    = $request->file('file');
    //     $results = Excel::load($file->getRealPath())->get();
    //
    //     try {
    //         $tmpCollect = $results[0];
    //         $arrTmp     = [];
    //         $khoaHocID  = KhoaHoc::hienTaiHoacTaoMoi()->id;
    //         $lopHocColl = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();
    //         $tmpRule    = [
    //             'ho_va_ten'     => 'required',
    //             'ngay_sinh'     => 'required|date_format:Y-m-d',
    //             'ngay_rua_toi'  => 'nullable|date_format:Y-m-d',
    //             'ngay_ruoc_le'  => 'nullable|date_format:Y-m-d',
    //             'ngay_them_suc' => 'nullable|date_format:Y-m-d',
    //         ];
    //
    //         $tmpCollect = $tmpCollect->filter(function ($c) {
    //             return $c->ho_va_ten && $c->ngay_sinh;
    //         })->map(function ($c) use ($library) {
    //             $c['ngay_sinh']     = $library->chuanHoaNgay($c['ngay_sinh']);
    //             $c['ngay_rua_toi']  = $c['ngay_rua_toi'] ? $library->chuanHoaNgay($c['ngay_rua_toi']) : null;
    //             $c['ngay_ruoc_le']  = $c['ngay_ruoc_le'] ? $library->chuanHoaNgay($c['ngay_ruoc_le']) : null;
    //             $c['ngay_them_suc'] = $c['ngay_them_suc'] ? $library->chuanHoaNgay($c['ngay_them_suc']) : null;
    //
    //             return $c;
    //         });
    //
    //         foreach ($tmpCollect as $c) {
    //             $validator = Validator::make($c->toArray(), $tmpRule);
    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'error' => $validator->errors(),
    //                 ], 400);
    //             }
    //
    //             $tmpLop = $lopHocColl->filter(function ($lh) use ($c) {
    //                 return $lh->nganh == $c->nganh && $lh->cap == $c->cap && $lh->doi == $c->doi;
    //             })->first();
    //
    //             if ($tmpLop) {
    //                 $c['lop_hoc_id']  = $tmpLop->id;
    //                 $c['lop_hoc_ten'] = $tmpLop->taoTen();
    //             }
    //         }
    //
    //         return response()->json([
    //             'data' => array_merge([], $tmpCollect->toArray()),
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => 'Kiểm tra lại định dạng tập tin.',
    //         ], 400);
    //     }
    // }

    // public function postTao(Request $request, Library $library)
    // {
    //     if (!$request->has('data')) {
    //         return response()->json([
    //             'error' => 'Không thấy dữ liệu.',
    //         ], 400);
    //     }
    //
    //     $resultArr   = [];
    //     $taiKhoanArr = $request->data;
    //     $khoaHocID   = KhoaHoc::hienTaiHoacTaoMoi()->id;
    //     $lopHocColl  = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();
    //
    //     DB::beginTransaction();
    //     foreach ($taiKhoanArr as $taiKhoan) {
    //         $newItem = TaiKhoan::taoTaiKhoan($taiKhoan);
    //         if (isset($taiKhoan['lop_hoc_id'])) {
    //             $tmpLop = $lopHocColl->filter(function ($lh) use ($taiKhoan) {
    //                 return $lh->id == $taiKhoan['lop_hoc_id'];
    //             })->first();
    //
    //             if ($tmpLop) {
    //                 App::make('TNTT\Controllers\LopHocController')->themThanhVien($tmpLop, [$newItem->id]);
    //                 $newItem['lop_hoc_ten'] = $tmpLop->taoTen();
    //             }
    //         }
    //         $resultArr[] = $newItem;
    //     }
    //
    //     $arrRow[] = [
    //         'Mã Số',
    //         'Họ và Tên',
    //         'Tên',
    //         'Lớp',
    //         'Loại Tài Khoản',
    //         'Trạng Thái',
    //         'Tên Thánh',
    //         'Giới Tính',
    //         'Ngày Sinh',
    //         'Ngày Rửa Tội',
    //         'Ngày Ruớc Lễ',
    //         'Ngày Thêm Sức',
    //         'Điện Thoại',
    //         'Địa Chỉ',
    //     ];
    //     foreach ($resultArr as $item) {
    //         $arrRow[] = [
    //             $item->id,
    //             $item->ho_va_ten,
    //             $item->ten,
    //             $item->lop_hoc_ten,
    //             $item->loai_tai_khoan,
    //             $item->trang_thai,
    //             $item->ten_thanh,
    //             $item->gioi_tinh,
    //             $library->chuanHoaNgay($item->ngay_sinh),
    //             $library->chuanHoaNgay($item->ngay_rua_toi),
    //             $library->chuanHoaNgay($item->ngay_ruoc_le),
    //             $library->chuanHoaNgay($item->ngay_them_suc),
    //             $item->dien_thoai,
    //             $item->dia_chi,
    //         ];
    //     }
    //
    //     $file = Excel::create('TaoMoi_TaiKhoan_'.date('d-m-Y'), function ($excel) use ($arrRow) {
    //         $excel->sheet('Danh Sách', function ($sheet) use ($arrRow) {
    //             $sheet->fromArray($arrRow)
    //                 ->setFreeze('C2');
    //         });
    //     })->store('xlsx', '/tmp', true);
    //     DB::commit();
    //
    //     return response()->json([
    //         'data' => $resultArr,
    //         'file' => $file['file'],
    //     ]);
    // }
}
