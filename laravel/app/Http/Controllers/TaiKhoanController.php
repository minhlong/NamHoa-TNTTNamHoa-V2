<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use App\Http\Requests\MatKhauFormRequest;
use App\Http\Requests\TaiKhoanFormRequest;
use Illuminate\Http\Request;
use App\Services\Library;
use App\KhoaHoc;
use App\LopHoc;
use App\TaiKhoan;
use App\DiemDanh;
use App\DiemSo;

class TaiKhoanController extends Controller
{
    /**
     * @param TaiKhoan $taiKhoan
     * @param Library $library
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDanhSach(TaiKhoan $taiKhoan, Request $request)
    {
        $taiKhoan = $taiKhoan->locDuLieu()->withTrashed()->get();

        // Thư mời - tạo mới - Tìm kiếm thông tin nên hiển thị luôn lớp học
        if ($request->has('loadLopHoc') && $request->has('khoa')) {
            $taiKhoan->load(['lop_hoc' => function ($query) use ($request) {
                $query->where('khoa_hoc_id', $request->khoa);
            }])->map(function ($c) {
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
     * @param TaiKhoan $taiKhoan
     * @return mixed
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
        $file = \Excel::create('Danh Sach Tai Khoan_' . date('d-m-Y'), function ($excel) use ($taiKhoan, $lopHoc, $request, $library) {
            $khoaID = $request->get('khoa');

            $arrRow = $this->generateTaiKhoanData($taiKhoan, $khoaID, $library);
            $excel->sheet('Danh Sách', function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow)
                    ->setFreeze('C2');
            });

            if (!$khoaID) {
                return;
            }

            $arrData = $this->getTongKet($lopHoc, $request);
            $arrRow = $this->generateTongKetData($arrData, $library);
            $excel->sheet('Tổng Kết - Khóa ' . $khoaID, function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow)->setFreeze('D4');
            });

        })->store('xlsx', '/tmp', true);

        return response()->json([
            'data' => $file['file'],
        ]);
    }

    protected function generateTaiKhoanData($taiKhoan, $khoaID, $library) {
        $taiKhoan = $taiKhoan->locDuLieu()->withTrashed();

        if ($khoaID) {
            $taiKhoan->with(['lop_hoc' => function ($q) use ($khoaID){
                $q->locDuLieu();
            }]);
        }

        // Generate Data
        $arrRow[] = [
            'Mã Số',
            'Họ và Tên',
            'Tên',
            'Lớp',
            'Loại Tài Khoản',
            'Trạng Thái',
            'Tên Thánh',
            'Giới Tính',
            'Ngày Sinh',
            'Ngày Rửa Tội',
            'Ngày Ruớc Lễ',
            'Ngày Thêm Sức',
            'Email',
            'Điện Thoại',
            'Địa Chỉ',
            'Giáo Họ',
            'Ghi Chú',
        ];
        foreach ($taiKhoan->get() as $item) {
            $tmpTenLop = null;
            if ($khoaID) {
                $tmpTenLop = $item->lop_hoc->first() ? $item->lop_hoc->first()->taoTen() : null;
            }
            $arrRow[] = [
                $item->id,
                $item->ho_va_ten,
                $item->ten,
                $tmpTenLop,
                $item->loai_tai_khoan,
                $item->trang_thai,
                $item->ten_thanh,
                $item->gioi_tinh,
                $library->chuanHoaNgay($item->ngay_sinh),
                $library->chuanHoaNgay($item->ngay_rua_toi),
                $library->chuanHoaNgay($item->ngay_ruoc_le),
                $library->chuanHoaNgay($item->ngay_them_suc),
                $item->email,
                $item->dien_thoai,
                $item->dia_chi,
                $item->giao_ho,
                $item->ghi_chu,
            ];
        }
        return $arrRow;
    }

    /**
     * @param $arrData
     * @param $library
     * @return array
     */
    protected function generateTongKetData($arrData, $library)
    {
        $arrRow = [];
        $arrHeaderLine1 = $arrHeaderLine2 = [
            'Mã Số',
            'Tên Thánh',
            'Họ và Tên',
            'Tên',
            'Lớp',
            'Học Lực',
            'Loại Học Lực',
            'Chuyên Cần',
            'Loại Chuyên Cần',
            '(HL+CC)/2',
            'Xếp Hạng',
            'Ghi Chú',
        ];
        foreach ($arrData['Data'] as $item) {
            $arrRow[$item['id']] = [
                'id'            => $item['id'],
                'ten_thanh'     => $item['ten_thanh'],
                'ho_va_ten'     => $item['ho_va_ten'],
                'ten'           => $item['ten'],
                'lop'           => isset($item['pivot']['tenLop']) ? $item['pivot']['tenLop'] : null,
                'hoc_luc'       => $item['pivot']['hoc_luc'],
                'loaiHocLuc'    => $item['pivot']['loaiHocLuc'],
                'chuyen_can'    => $item['pivot']['chuyen_can'],
                'loaiChuyenCan' => $item['pivot']['loaiChuyenCan'],
                'tb_canam'      => ($item['pivot']['chuyen_can'] + $item['pivot']['hoc_luc']) / 2,
                'xep_hang'      => $item['pivot']['xep_hang'],
                'ghi_chu'       => $item['pivot']['ghi_chu'],
            ];
        }
        foreach ($arrData['SoDot'] as $dot) {
            foreach ($arrData['SoLan'] as $lan) {
                $arrHeaderLine1[] = "Lần $dot";
                $arrHeaderLine2[] = "Đợt $lan";
                foreach ($arrRow as $id => &$info) {
                    $info["Diem | Dot $dot - Lan $lan"] = isset($arrData['DiemSo'][$id][$dot][$lan]) ? $arrData['DiemSo'][$id][$dot][$lan] : null;
                }
            }
        }
        foreach ($arrData['DiemDanh'] as $ngay => $item) {
            $arrHeaderLine1[] = $library->chuanHoaNgay($ngay);
            $arrHeaderLine1[] = $library->chuanHoaNgay($ngay);
            $arrHeaderLine2[] = 'Đi Lễ';
            $arrHeaderLine2[] = 'Đi Học';
            foreach ($arrRow as $id => &$info) {
                $info[$ngay . ' - Di Le'] = isset($item[$id]['di_le']) ? $item[$id]['di_le'] : null;
                $info[$ngay . ' - Di Hoc'] = isset($item[$id]['di_hoc']) ? $item[$id]['di_hoc'] : null;
            }
        }
        $arrRow = array_merge(
            [$arrHeaderLine1],
            [$arrHeaderLine2],
            $arrRow
        );

        return $arrRow;
    }

    /**
     * @param LopHoc $lopHoc Nếu không có lớp cụ thể, sẽ export toàn bộ học viên của khóa hiện tại
     * @return array
     */
    public function getTongKet(LopHoc $lopHoc, Request $request)
    {
        $arrResult = [
            'Data'     => [],
            'DiemDanh' => [],
            'DiemSo'   => [],
            'SoDot'    => [],
            'SoLan'    => [],
        ];
        $arrHocVien = collect();

        if($lopHoc->id) {
            $khoaID = $lopHoc->khoa_hoc_id;
            $arrLop[] = $lopHoc;
        } else {
            $khoaID = $request->get('khoa');
            $arrLop = LopHoc::locDuLieu()->get();
        }
        $khoaHoc = KhoaHoc::findOrFail($khoaID);

        foreach ($arrLop as $lopHoc) {
            $arrTmp = $lopHoc->hoc_vien()->locDuLieu()->get();
            $tenLop = $lopHoc->taoTen(true);
            foreach ($arrTmp as &$hocVien) {
                $hocVien->pivot->tenLop = $tenLop;
                $arrHocVien[] = $hocVien;
            }
        }

        // Add Xep Loai Chuyen Can - Hoc Luc
        $arrLoai = [
            'TB',
            'KHA',
            'GIOI',
        ];
        foreach ($arrHocVien as &$hocVien) {
            $hocVien->pivot->loaiChuyenCan = $hocVien->pivot->loaiHocLuc = 'YEU';
            foreach ($arrLoai as $loai) {
                if ($hocVien->pivot->chuyen_can >= $khoaHoc->xep_loai['CHUYEN_CAN'][$loai]) {
                    $hocVien->pivot->loaiChuyenCan = $loai;
                }
                if ($hocVien->pivot->hoc_luc >= $khoaHoc->xep_loai['HOC_LUC'][$loai]) {
                    $hocVien->pivot->loaiHocLuc = $loai;
                }
                $hocVien->pivot->tb_canam = ($hocVien->pivot->chuyen_can + $hocVien->pivot->hoc_luc) / 2;
            }
        }
        $arrResult['Data'] = $arrHocVien->toArray();
        // Add Diem Danh
        $arrDiemDanh = DiemDanh::whereIn('tai_khoan_id',
            $arrHocVien->pluck('id'))
            ->whereBetween('ngay',
                [$khoaHoc->ngay_bat_dau, $khoaHoc->ngay_ket_thuc])
            ->whereNull('phan_loai')
            ->orderBy('ngay')
            ->get();
        foreach ($arrDiemDanh as $item) {
            $arrResult['DiemDanh'][$item->ngay] [$item->tai_khoan_id] = [
                'di_le'  => $item->di_le,
                'di_hoc' => $item->di_hoc,
            ];
        }
        // Add Diem So
        $arrDiemSo = DiemSo::whereIn('tai_khoan_id', $arrHocVien->pluck('id'))
            ->where('khoa_hoc_id', $khoaHoc->id)
            ->whereNull('phan_loai')
            ->orderBy('dot')
            ->orderBy('lan')
            ->get();
        foreach ($arrDiemSo as $item) {
            $arrResult['DiemSo'] [$item->tai_khoan_id] [$item->dot] [$item->lan] = $item->diem;
            $arrResult['SoDot'][$item->dot] = $item->dot;
            $arrResult['SoLan'][$item->lan] = $item->lan;
        }
        $arrResult['SoDot'] = array_values($arrResult['SoDot']);
        $arrResult['SoLan'] = array_values($arrResult['SoLan']);

        return $arrResult;
    }

    public function postThemSuc(Request $request) {
        try {
            TaiKhoan::whereIn('id', $request->get('tai_khoan'))->update(['ngay_them_suc' => $request->get('ngay_them_suc')]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sai định dạng ngày'
            ], 400);
        }

        return response()->json();
    }

    public function postRuocLe(Request $request) {
        try {
            TaiKhoan::whereIn('id', $request->get('tai_khoan'))->update(['ngay_ruoc_le' => $request->get('ngay_ruoc_le')]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sai định dạng ngày'
            ], 400);
        }

        return response()->json();
    }

    /**
     * Luu Thong Tin Tai Khoan.
     * @param TaiKhoan $taiKhoan
     * @param TaiKhoanFormRequest $taiKhoanFormRequest
     * @return string
     */
    public function postUpdate(TaiKhoan $taiKhoan, TaiKhoanFormRequest $taiKhoanFormRequest)
    {
        if(!\Entrust::can('tai-khoan') && $taiKhoan->id != \Auth::user()->id) {
            abort(403);
        }

        $taiKhoan->fill($taiKhoanFormRequest->all());
        $taiKhoan->save();
        // Update Trang Thai
        if ($taiKhoan->trang_thai == 'TAM_NGUNG' && !$taiKhoan->trashed()) {
            $taiKhoan->delete();
        } elseif ($taiKhoan->trang_thai == 'HOAT_DONG' && $taiKhoan->trashed()) {
            $taiKhoan->restore();
        }

        return $this->getThongTin($taiKhoan);
    }

    public function postMatKhau(TaiKhoan $taiKhoan)
    {
        if(!\Entrust::can('tai-khoan') && $taiKhoan->id != \Auth::user()->id) {
            abort(403);
        }

        $taiKhoan->capNhatMatKhau(\Request::get('mat_khau'));
        $taiKhoan->save();

        return response()->json($taiKhoan);
    }

    public function postXoa(TaiKhoan $taiKhoan)
    {
        try {
            $taiKhoan->forceDelete();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Liên hệ quản trị'
            ], 400);
        }
        
        return response()->json();
    }

    public function postTapTin(Request $request, Library $library) {
        if(!$request->hasFile('file')) {
           return response()->json([
                'error' => 'Không tìm thấy tập tin.',
           ], 400);
        }

        $file = $request->file('file');
        $results = \Excel::load($file->getRealPath())->get();

        try {
            $tmpCollect = $results[0];
            $arrTmp = [];
            $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
            $lopHocColl = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();
            $tmpRule = [
                'ho_va_ten' => 'required',
                'ngay_sinh' => 'required|required|date_format:Y-m-d',
                'ngay_rua_toi' => 'nullable|date_format:Y-m-d',
                'ngay_ruoc_le' => 'nullable|date_format:Y-m-d',
                'ngay_them_suc' => 'nullable|date_format:Y-m-d',
            ];

            $tmpCollect = $tmpCollect->filter(function ($c) {
                return $c->ho_va_ten && $c->ngay_sinh;
            })->map(function ($c) use ($library) {
                $c['ngay_sinh'] = $library->chuanHoaNgay($c['ngay_sinh']);
                $c['ngay_rua_toi'] = $c['ngay_rua_toi'] ? $library->chuanHoaNgay($c['ngay_rua_toi']) : null;
                $c['ngay_ruoc_le'] = $c['ngay_ruoc_le'] ? $library->chuanHoaNgay($c['ngay_ruoc_le']) : null;
                $c['ngay_them_suc'] = $c['ngay_them_suc'] ? $library->chuanHoaNgay($c['ngay_them_suc']) : null;

                return $c;
            });

            foreach ($tmpCollect as $c) {
                $validator = \Validator::make($c->toArray(), $tmpRule);
                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->errors()
                    ], 400);
                }

                $tmpLop = $lopHocColl->filter( function ($lh) use ($c) {
                    return $lh->nganh == $c->nganh && $lh->cap == $c->cap && $lh->doi == $c->doi;
                })->first();

                if ($tmpLop) {
                    $c['lop_hoc_id'] = $tmpLop->id;
                    $c['lop_hoc_ten'] = $tmpLop->taoTen();
                }
            }

            return response()->json([
                'data' => array_merge([],$tmpCollect->toArray()),
            ]);
        } catch (\Exception $e) {
           return response()->json([
                'error' => 'Kiểm tra lại định dạng tập tin.',
           ], 400);        
        }
    }

    public function postTao(Request $request, Library $library) {
        if(!$request->has('data')) {
           return response()->json([
                'error' => 'Không thấy dữ liệu.',
           ], 400);
        }

        $resultArr = [];
        $taiKhoanArr = $request->data;
        $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
        $lopHocColl = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();

        \DB::beginTransaction();
        foreach ($taiKhoanArr as $taiKhoan) {
            $newItem = TaiKhoan::taoTaiKhoan($taiKhoan);
            if (isset($taiKhoan['lop_hoc_id'])) {
                $tmpLop = $lopHocColl->filter( function ($lh) use ($taiKhoan) {
                    return $lh->id == $taiKhoan['lop_hoc_id'];
                })->first();

                if ($tmpLop) {
                    App::make('App\Http\Controllers\LopHocController')->themThanhVien($tmpLop, [$newItem->id]);
                    $newItem['lop_hoc_ten'] = $tmpLop->taoTen();
                }
            }
            $resultArr[] = $newItem;
        }
        
        $arrRow[] = [
            'Mã Số',
            'Họ và Tên',
            'Tên',
            'Lớp',
            'Loại Tài Khoản',
            'Trạng Thái',
            'Tên Thánh',
            'Giới Tính',
            'Ngày Sinh',
            'Ngày Rửa Tội',
            'Ngày Ruớc Lễ',
            'Ngày Thêm Sức',
            'Điện Thoại',
            'Địa Chỉ',
        ];
        foreach ($resultArr as $item) {
            $arrRow[] = [
                $item->id,
                $item->ho_va_ten,
                $item->ten,
                $item->lop_hoc_ten,
                $item->loai_tai_khoan,
                $item->trang_thai,
                $item->ten_thanh,
                $item->gioi_tinh,
                $library->chuanHoaNgay($item->ngay_sinh),
                $library->chuanHoaNgay($item->ngay_rua_toi),
                $library->chuanHoaNgay($item->ngay_ruoc_le),
                $library->chuanHoaNgay($item->ngay_them_suc),
                $item->dien_thoai,
                $item->dia_chi,
            ];
        }

        $file = \Excel::create('TaoMoi_TaiKhoan_' . date('d-m-Y'), function ($excel) use ($arrRow) {
            $excel->sheet('Danh Sách', function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow)
                    ->setFreeze('C2');
            });
        })->store('xlsx', '/tmp', true);
        \DB::commit();

        return response()->json([
            'data' => $resultArr,
            'file' => $file['file'],
        ]);
    }
}
