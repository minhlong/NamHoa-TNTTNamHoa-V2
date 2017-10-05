<?php
namespace App\Http\Controllers;

use App\Services\Library;
use Illuminate\Http\Request;

use App\DiemDanh;
use App\DiemSo;
use App\KhoaHoc;
use App\LopHoc;

class LopHocController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDanhSachTheoKhoa($khoaHocID, LopHoc $lopHoc)
    {
        $lopHoc = $lopHoc->where('khoa_hoc_id', $khoaHocID)->locDuLieu()->get()->load('huynh_truong')->map(function ($c) {
            $c['ten'] = $c->taoTen();
            return $c;
        });

        return response()->json([
            'data' => $lopHoc,
        ]);
    }

    /**
     * @param LopHoc $lopHoc
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getThongTin(LopHoc $lopHoc)
    {
        $lopHoc->load(['huynh_truong', 'hoc_vien']);
        $lopHoc->ten = $lopHoc->taoTen();

        return response()->json($lopHoc);
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
            $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
            $lopHocColl = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();

            $tmpCollect = $tmpCollect->filter(function ($c) {
                return $c->nganh;
            });

            foreach ($lopHocColl as $c) {
                $tmpCollect = $tmpCollect->filter( function ($lh) use ($c) {
                    return !($lh->nganh == $c->nganh && $lh->cap == $c->cap && $lh->doi == $c->doi);
                });
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

    public function postTao(Request $request) {
        if(!$request->has('data')) {
           return response()->json([
                'error' => 'Không thấy dữ liệu.',
           ], 400);
        }

        $resultArr = [];
        $tmpItemArr = $request->data;
        $khoaHocID = KhoaHoc::hienTaiHoacTaoMoi()->id;
        $lopHocColl = LopHoc::where('khoa_hoc_id', $khoaHocID)->get();

        \DB::beginTransaction();
        foreach ($tmpItemArr as $tmpItem) {
            try {
                $lopHoc = LopHoc::create(array_merge([
                    'khoa_hoc_id' => $khoaHocID,
                ], $tmpItem));   
            } catch (\Exception $e) {
                continue;
            }

            $lopHoc->ten = $lopHoc->taoTen();
            $resultArr[] = $lopHoc;
        }
        
        $arrRow[] = [
            'Mã Lớp',
            'Tên',
            'Vị Trí Học',
        ];
        foreach ($resultArr as $item) {
            $arrRow[] = [
                $item->id,
                $item->ten,
                $item->vi_tri_hoc,
            ];
        }

        $file = \Excel::create('TaoMoi_LopHoc_' . date('d-m-Y'), function ($excel) use ($arrRow) {
            $excel->sheet('Danh Sách', function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow);
            });
        })->store('xlsx', '/tmp', true);
        \DB::commit();

        return response()->json([
            'data' => $resultArr,
            'file' => $file['file'],
        ]);
    }

    /**
     * @param LopHoc $lopHoc
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUpdate(LopHoc $lopHoc)
    {
        try {
            $lopHoc->fill(\Request::all());
            $lopHoc->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Kiểm tra lại thông tin.',
           ], 400);
        }

        return $this->getThongTin($lopHoc);
    }

    public function postXoa(LopHoc $lopHoc)
    {
        $lopHoc->delete();
        return response()->json();
    }

    /**
     * @param LopHoc $lopHoc
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postThemThanhVien(LopHoc $lopHoc, Request $request)
    {
        if(!$request->has('id') || !is_array($request->id)) {
           return response()->json([
                'error' => 'Không thấy dữ liệu.',
           ], 400);
        }

        $this.attachHocVien($lopHoc, $request->id);
        return $this->getThongTin($lopHoc);
    }

    public function themThanhVien(LopHoc $lopHoc, $arrID)
    {
        $lopHoc->thanh_vien()->attach($arrID);
        $lopHoc->tinhTongKet();
    }

    /**
     * @param LopHoc $lopHoc
     * @param tmpItem $tmpItem
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postXoaThanhVien(LopHoc $lopHoc, Request $request)
    {
        if(!$request->has('id') || !is_array($request->id)) {
           return response()->json([
                'error' => 'Không thấy dữ liệu.',
           ], 400);
        }

        $arrID = $request->id;
        $lopHoc->thanh_vien()->detach($arrID);
        return $this->getThongTin($lopHoc);
    }
    // /**
    //  * @param LopHoc $lopHoc
    //  * @param tmpItem $tmpItem
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function postHocVien(LopHoc $lopHoc, tmpItem $tmpItem)
    // {
    //     $this->attachHocVien($lopHoc, $tmpItem);

    //     return $this->getThongTin($lopHoc);
    // }

    // /**
    //  * @param LopHoc $lopHoc
    //  * @param tmpItem $tmpItem
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function deleteHocVien(LopHoc $lopHoc, tmpItem $tmpItem)
    // {
    //     $lopHoc->hoc_vien()->detach($tmpItem->id);
    //     $lopHoc->tinhTongKet();

    //     return $this->getThongTin($lopHoc);
    // }

    // /**
    //  * @param LopHoc $lopHoc
    //  * @param DiemDanh $diemDanh
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function getChuyenCan(LopHoc $lopHoc, DiemDanh $diemDanh, Library $library)
    // {
    //     $arrHocVien = $lopHoc->hoc_vien()->pluck('tai_khoan_id')->toArray();
    //     $sDate = $this->getSundayFromADate($lopHoc, $library, \Request::get('ngay_hoc'));
    //     $arrOptions = [
    //         'lop_hoc_id' => $lopHoc->id,
    //         'ngay_hoc'   => $sDate,
    //     ];
    //     $result = array_merge([
    //         'data' => $diemDanh->getChuyenCanData($arrHocVien, $sDate)
    //     ], \Auth::user()->getPhanQuyen($arrOptions));

    //     return response()->json($result);
    // }

    // public function postChuyenCan(LopHoc $lopHoc, DiemDanh $diemDanh)
    // {
    //     $diemDanh->luuChuyenCan($lopHoc);

    //     return response()->json(true);
    // }

    // public function getHocLuc(LopHoc $lopHoc, DiemSo $diemSo)
    // {
    //     $arrHocVien = $lopHoc->hoc_vien()->pluck('tai_khoan_id')->toArray();
    //     $tmpDot = \Request::get('dot');
    //     $arrOptions = [
    //         'lop_hoc_id' => $lopHoc->id,
    //         'dotKT'      => $tmpDot,
    //     ];
    //     $result = array_merge([
    //         'data' => $diemSo->getHocLuc($arrHocVien, $lopHoc->khoa_hoc, $tmpDot)
    //     ], \Auth::user()->getPhanQuyen($arrOptions));

    //     return response()->json(empty($result) ? null : $result);
    // }

    // public function postHocLuc(LopHoc $lopHoc, DiemSo $diemSo)
    // {
    //     $diemSo->luuHocLuc($lopHoc);

    //     return response()->json(true);
    // }

    // /**
    //  * @param LopHoc $lopHoc Nếu không có lớp cụ thể, sẽ export toàn bộ học viên của khóa hiện tại
    //  * @return array
    //  */
    // public function getTongKet(LopHoc $lopHoc = null)
    // {
    //     $arrResult = [
    //         'Data'     => [],
    //         'DiemDanh' => [],
    //         'DiemSo'   => [],
    //         'SoDot'    => [],
    //         'SoLan'    => [],
    //     ];
    //     $arrHocVien = collect();
    //     if (!$lopHoc) {
    //         $khoaHoc = KhoaHoc::hienTaiHoacTaoMoi();
    //         $arrLop = LopHoc::whereKhoaHocId($khoaHoc->id)->get();
    //         foreach ($arrLop as $lopHoc) {
    //             $arrTmp = $lopHoc->hoc_vien()->with('than_nhan')->get();
    //             $tenLop = $lopHoc->taoTen(true);
    //             foreach ($arrTmp as &$hocVien) {
    //                 $hocVien->pivot->tenLop = $tenLop;
    //                 $arrHocVien[] = $hocVien;
    //             }
    //         }
    //     } else {
    //         $khoaHoc = $lopHoc->khoa_hoc;
    //         $arrHocVien = $lopHoc->hoc_vien()->with('than_nhan')->get();
    //     }
    //     // Add Xep Loai Chuyen Can - Hoc Luc
    //     $arrLoai = [
    //         'TB',
    //         'KHA',
    //         'GIOI',
    //     ];
    //     foreach ($arrHocVien as &$hocVien) {
    //         $hocVien->pivot->loaiChuyenCan = $hocVien->pivot->loaiHocLuc = 'YEU';
    //         foreach ($arrLoai as $loai) {
    //             if ($hocVien->pivot->chuyen_can >= $khoaHoc->xep_loai['CHUYEN_CAN'][$loai]) {
    //                 $hocVien->pivot->loaiChuyenCan = $loai;
    //             }
    //             if ($hocVien->pivot->hoc_luc >= $khoaHoc->xep_loai['HOC_LUC'][$loai]) {
    //                 $hocVien->pivot->loaiHocLuc = $loai;
    //             }
    //             $hocVien->pivot->tb_canam = ($hocVien->pivot->chuyen_can + $hocVien->pivot->hoc_luc) / 2;
    //         }
    //     }
    //     $arrResult['Data'] = $arrHocVien->toArray();
    //     // Add Diem Danh
    //     $arrDiemDanh = DiemDanh::whereIn('tai_khoan_id',
    //         $arrHocVien->pluck('id'))
    //         ->whereBetween('ngay',
    //             [$khoaHoc->ngay_bat_dau, $khoaHoc->ngay_ket_thuc])
    //         ->whereNull('phan_loai')
    //         ->orderBy('ngay')
    //         ->get();
    //     foreach ($arrDiemDanh as $item) {
    //         $arrResult['DiemDanh'][$item->ngay] [$item->tai_khoan_id] = [
    //             'di_le'  => $item->di_le,
    //             'di_hoc' => $item->di_hoc,
    //         ];
    //     }
    //     // Add Diem So
    //     $arrDiemSo = DiemSo::whereIn('tai_khoan_id', $arrHocVien->pluck('id'))
    //         ->where('khoa_hoc_id', $khoaHoc->id)
    //         ->whereNull('phan_loai')
    //         ->orderBy('dot')
    //         ->orderBy('lan')
    //         ->get();
    //     foreach ($arrDiemSo as $item) {
    //         $arrResult['DiemSo'] [$item->tai_khoan_id] [$item->dot] [$item->lan] = $item->diem;
    //         $arrResult['SoDot'][$item->dot] = $item->dot;
    //         $arrResult['SoLan'][$item->lan] = $item->lan;
    //     }
    //     $arrResult['SoDot'] = array_values($arrResult['SoDot']);
    //     $arrResult['SoLan'] = array_values($arrResult['SoLan']);

    //     return $arrResult;
    // }

    // public function postXepHang(LopHoc $lopHoc, tmpItem $tmpItem)
    // {
    //     $hocVien = $lopHoc->hoc_vien()
    //         ->where('tai_khoan_id', $tmpItem->id)
    //         ->first();
    //     $hocVien->pivot->xep_hang = \Request::get('hang');
    //     $hocVien->pivot->ghi_chu = \Request::get('ghi_chu_hang');
    //     $hocVien->pivot->nhan_xet = \Request::get('nhan_xet');
    //     $hocVien->pivot->save();
    // }

    // public function getHocVienYeu()
    // {
    //     $khoahoc = KhoaHoc::hienTaiHoacTaoMoi();
    //     $tmpItems = tmpItem::whereHas('lop_hoc',
    //         function ($q) use ($khoahoc) {
    //             $q->where('khoa_hoc_id', $khoahoc->id)
    //                 ->where(function ($query) {
    //                     $query->where('chuyen_can', '<=',
    //                         \Request::get('chuyen_can') ? (float)\Request::get('chuyen_can') : 5)
    //                         ->orWhere('hoc_luc', '<=',
    //                             \Request::get('hoc_luc') ? (float)\Request::get('hoc_luc') : 5);
    //                 });
    //             if (\Request::get('nganh')) {
    //                 $q->where('nganh', \Request::get('nganh'));
    //             }
    //             if (\Request::get('cap')) {
    //                 $q->where('cap', \Request::get('cap'));
    //             }
    //             if (\Request::get('doi')) {
    //                 $q->where('doi', \Request::get('doi'));
    //             }
    //         });
    //     $records = [
    //         'data'                 => [],
    //         'draw'                 => \Request::get('draw'),
    //         'iTotalDisplayRecords' => $tmpItems->get()->count(), // Before Skipt data
    //     ];
    //     if (($length = \Request::get('length')) > 0) {
    //         $tmpItems = $tmpItems->skip(\Request::get('start'))->take($length);
    //     }
    //     // Load Class for current Year
    //     $tmpItems = $tmpItems->with([
    //         'lop_hoc' => function ($query) use ($khoahoc) {
    //             $query->where('khoa_hoc_id', $khoahoc->id);
    //         }
    //     ]);
    //     foreach ($tmpItems->get() as $item) {
    //         $records['data'][] = [
    //             'ten'        => "$item->ten_thanh $item->ho_va_ten",
    //             'chuyen_can' => $item->lop_hoc[0]->pivot['chuyen_can'],
    //             'hoc_luc'    => $item->lop_hoc[0]->pivot['hoc_luc'],
    //             'ten_lop'    => $item->lop_hoc[0]->taoTen(),
    //             'id'         => $item->lop_hoc[0]->id,
    //         ];
    //     }

    //     return response()->json($records);
    // }

    // public function getDownload(LopHoc $lopHoc, Library $library)
    // {
    //     if ($lopHoc->id) {
    //         // Tao Du Lieu Cho 1 Lop
    //         $fileName = $lopHoc ? $lopHoc->taoTen() : null;
    //     } else {
    //         // Tao Du Lieu Toan Khoa Hien Tai
    //         $lopHoc = null;
    //         $fileName = 'Khóa ' . KhoaHoc::hienTaiHoacTaoMoi()->id;
    //     }
    //     \Excel::create($fileName . '-' . strtotime('now'),
    //         function ($excel) use ($lopHoc, $library) {
    //             $arrData = $this->getTongKet($lopHoc);
    //             $arrRow = $this->generatetmpItemData($arrData, $library);
    //             $excel->sheet('Danh Sách Lớp', function ($sheet) use ($arrRow) {
    //                 $sheet->fromArray($arrRow, null, null, null, false)
    //                     ->setFreeze('D2');
    //             });
    //             $arrRow = $this->generateTongKetData($arrData, $library);
    //             $excel->sheet('Tổng Kết', function ($sheet) use ($arrRow) {
    //                 $sheet->fromArray($arrRow, null, null, null, false)
    //                     ->setMergeColumn([
    //                         'columns' => range('A', 'M'),
    //                         'rows'    => [[1, 2],]
    //                     ])->setFreeze('D3');
    //             });
    //             $arrRow = $this->generateThanNhanData($arrData, $library);
    //             $excel->sheet('Người Thân', function ($sheet) use ($arrRow) {
    //                 $sheet->fromArray($arrRow, null, null, null, false)
    //                     ->setFreeze('D2');
    //             });
    //         })->download('xls');
    // }

    // protected function generateThanNhanData($arrData, $library)
    // {
    //     $arrRow = [
    //         [
    //             'Mã Số',
    //             'Tên Thánh',
    //             'Họ và Tên',
    //             'Tên',
    //             'Ngày Sinh',
    //             'Lớp',
    //             'Mã Gen',
    //             'Loại Quan Hệ',
    //             'Thông Tin',
    //             'Điện Thoại',
    //             'Địa Chỉ',
    //         ]
    //     ];
    //     $arrGEN = $this->checkGenAnhEm($arrData);
    //     foreach ($arrData['Data'] as $item) {
    //         foreach ($item['than_nhan'] as $than_nhan) {
    //             $arrRow[] = [
    //                 $item['id'],
    //                 $item['ten_thanh'],
    //                 $item['ho_va_ten'],
    //                 $item['ten'],
    //                 $library->chuanHoaNgay($item['ngay_sinh']),
    //                 isset($item['pivot']['tenLop']) ? $item['pivot']['tenLop'] : null,
    //                 isset($arrGEN[$item['gia_pha_id']]) && $arrGEN[$item['gia_pha_id']] > 1 ? $item['gia_pha_id'] : null,
    //                 $than_nhan['loai_quan_he'],
    //                 $than_nhan['ho_va_ten'],
    //                 $than_nhan['dien_thoai'],
    //                 $than_nhan['ghi_chu'],
    //             ];
    //         }
    //     }

    //     return $arrRow;
    // }

    // /**
    //  * @param $arrData
    //  * @param $library
    //  * @return array
    //  */
    // protected function generateTongKetData($arrData, $library)
    // {
    //     $arrRow = [];
    //     $arrHeaderLine1 = $arrHeaderLine2 = [
    //         'Mã Số',
    //         'Tên Thánh',
    //         'Họ và Tên',
    //         'Tên',
    //         'Mã Gen',
    //         'Lớp',
    //         'Học Lực',
    //         'Loại Học Lực',
    //         'Chuyên Cần',
    //         'Loại Chuyên Cần',
    //         '(HL+CC)/2',
    //         'Xếp Hạng',
    //         'Ghi Chú',
    //     ];
    //     $arrGEN = $this->checkGenAnhEm($arrData);
    //     foreach ($arrData['Data'] as $item) {
    //         $arrRow[$item['id']] = [
    //             'id'            => $item['id'],
    //             'ten_thanh'     => $item['ten_thanh'],
    //             'ho_va_ten'     => $item['ho_va_ten'],
    //             'ten'           => $item['ten'],
    //             'gen'           => isset($arrGEN[$item['gia_pha_id']]) && $arrGEN[$item['gia_pha_id']] > 1 ? $item['gia_pha_id'] : null,
    //             'lop'           => isset($item['pivot']['tenLop']) ? $item['pivot']['tenLop'] : null,
    //             'hoc_luc'       => $item['pivot']['hoc_luc'],
    //             'loaiHocLuc'    => $item['pivot']['loaiHocLuc'],
    //             'chuyen_can'    => $item['pivot']['chuyen_can'],
    //             'loaiChuyenCan' => $item['pivot']['loaiChuyenCan'],
    //             'tb_canam'      => ($item['pivot']['chuyen_can'] + $item['pivot']['hoc_luc']) / 2,
    //             'xep_hang'      => $item['pivot']['xep_hang'],
    //             'ghi_chu'       => $item['pivot']['ghi_chu'],
    //         ];
    //     }
    //     foreach ($arrData['SoDot'] as $dot) {
    //         foreach ($arrData['SoLan'] as $lan) {
    //             $arrHeaderLine1[] = "Lần $dot";
    //             $arrHeaderLine2[] = "Đợt $lan";
    //             foreach ($arrRow as $id => &$info) {
    //                 $info["Diem | Dot $dot - Lan $lan"] = isset($arrData['DiemSo'][$id][$dot][$lan]) ? $arrData['DiemSo'][$id][$dot][$lan] : null;
    //             }
    //         }
    //     }
    //     foreach ($arrData['DiemDanh'] as $ngay => $item) {
    //         $arrHeaderLine1[] = $library->chuanHoaNgay($ngay);
    //         $arrHeaderLine1[] = $library->chuanHoaNgay($ngay);
    //         $arrHeaderLine2[] = 'Đi Lễ';
    //         $arrHeaderLine2[] = 'Đi Học';
    //         foreach ($arrRow as $id => &$info) {
    //             $info[$ngay . ' - Di Le'] = isset($item[$id]['di_le']) ? $item[$id]['di_le'] : null;
    //             $info[$ngay . ' - Di Hoc'] = isset($item[$id]['di_hoc']) ? $item[$id]['di_hoc'] : null;
    //         }
    //     }
    //     $arrRow = array_merge(
    //         [$arrHeaderLine1],
    //         [$arrHeaderLine2],
    //         $arrRow
    //     );

    //     return $arrRow;
    // }

    // /**
    //  * @param $arrData
    //  * @param Library $library
    //  * @return array
    //  */
    // protected function generatetmpItemData($arrData, Library $library)
    // {
    //     $arrRow = [];
    //     $arrHeader = [
    //         'Mã Số',
    //         'Tên Thánh',
    //         'Họ và Tên',
    //         'Tên',
    //         'Trạng Thái',
    //         'Mã Gen',
    //         'Giới Tính',
    //         'Ngày Sinh',
    //         'Ngày Rửa Tội',
    //         'Ngày Ruớc Lễ',
    //         'Ngày Thêm Sức',
    //         'Email',
    //         'Điện Thoại',
    //         'Địa Chỉ',
    //         'Giáo Họ',
    //         'Ghi Chú',
    //     ];
    //     $arrGEN = $this->checkGenAnhEm($arrData);
    //     foreach ($arrData['Data'] as $item) {
    //         $arrRow[] = [
    //             $item['id'],
    //             $item['ten_thanh'],
    //             $item['ho_va_ten'],
    //             $item['ten'],
    //             $item['trang_thai'],
    //             isset($arrGEN[$item['gia_pha_id']]) && $arrGEN[$item['gia_pha_id']] > 1 ? $item['gia_pha_id'] : null,
    //             $item['gioi_tinh'],
    //             $library->chuanHoaNgay($item['ngay_sinh']),
    //             $library->chuanHoaNgay($item['ngay_rua_toi']),
    //             $library->chuanHoaNgay($item['ngay_ruoc_le']),
    //             $library->chuanHoaNgay($item['ngay_them_suc']),
    //             $item['email'],
    //             $item['dien_thoai'],
    //             $item['dia_chi'],
    //             $item['giao_ho'],
    //             $item['ghi_chu'],
    //         ];
    //     }
    //     $arrRow = array_merge([$arrHeader], $arrRow);

    //     return $arrRow;
    // }

    // /**
    //  * Check GEN Anh-Em
    //  * @param $arrData
    //  * @return array
    //  */
    // protected function checkGenAnhEm($arrData)
    // {
    //     $arrGEN = [];
    //     foreach ($arrData['Data'] as $item) {
    //         if ($item['gia_pha_id']) {
    //             @$arrGEN[$item['gia_pha_id']] += 1;
    //         }
    //     }

    //     return $arrGEN;
    // }

    // /**
    //  * @param LopHoc $lopHoc
    //  * @param Library $library
    //  * @param $ngay_hoc
    //  * @return mixed|null
    //  */
    // private function getSundayFromADate(LopHoc $lopHoc, Library $library, $ngay_hoc)
    // {
    //     // Trong pham vi 6 ngay
    //     $endDate = strtotime($ngay_hoc);
    //     $startDate = strtotime('-6day', $endDate);
    //     // Chỉ hiện ngày trong phạm vi của Khóa Học Tương Ứng
    //     if ($startDate < strtotime($lopHoc->khoa_hoc->ngay_bat_dau)) {
    //         $startDate = strtotime($lopHoc->khoa_hoc->ngay_bat_dau);
    //     }
    //     if ($endDate > strtotime($lopHoc->khoa_hoc->ngay_ket_thuc)) {
    //         $endDate = strtotime($lopHoc->khoa_hoc->ngay_ket_thuc);
    //     }
    //     $startDate = date('Y-m-d', $startDate);
    //     $endDate = date('Y-m-d', $endDate);
    //     // Lay ngay Chua Nhat
    //     $aDate = $library->SpecificDayBetweenDates($startDate, $endDate);

    //     return empty($aDate) ? null : array_shift($aDate);
    // }
}
