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
    public function getDanhSach(TaiKhoan $taiKhoan, Library $library)
    {
        $taiKhoan = $taiKhoan->locDuLieu()->withTrashed()->get();

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
        $file = \Excel::create('Danh Sach Tai Khoan_' . date('d-m-Y') . '_' . strtotime('now'), function ($excel) use ($taiKhoan, $lopHoc, $request, $library) {
            $khoaID = $request->get('khoa');

            $arrRow = $this->genTaiKhoan($taiKhoan, $khoaID, $library);
            $excel->sheet('Danh Sách', function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow, null, null, null, false)
                    ->setFreeze('C2');
            });

            if (!$khoaID) {
                return;
            }

            $arrData = $this->getTongKet($lopHoc, $request);
            $arrRow = $this->generateTongKetData($arrData, $library);
            $excel->sheet('Tổng Kết - Khóa ' . $khoaID, function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow, null, null, null, false)
                    ->setMergeColumn([
                        'columns' => range('A', 'L'),
                        'rows'    => [[1, 2],]
                    ])->setFreeze('D3');
            });

        })->store('xls', '/tmp', true);

        return response()->json([
            'data' => $file['file'],
        ]);
    }

    protected function genTaiKhoan($taiKhoan, $khoaID, $library) {
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
        $khoaHoc = KhoaHoc::findOrFail($request->get('khoa'));

        $arrResult = [
            'Data'     => [],
            'DiemDanh' => [],
            'DiemSo'   => [],
            'SoDot'    => [],
            'SoLan'    => [],
        ];
        $arrHocVien = collect();

        $arrLop = LopHoc::locDuLieu()->get();
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

    public function getDownloadFile($fileName)
    {
        return \Response::download("/tmp/$fileName");
    }

    /**
     * @param $id Image name
     * @param Library $library
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getHinhAnhDaiDien($id, Library $library)
    {
        $response = response($library->getProfileImage($id));
        $response->header('Content-Type', 'image/png');

        return $response;
    }
}
