<?php
namespace App\Http\Controllers;

use App\Http\Requests\TaiKhoanFormRequest;
use Illuminate\Support\Facades\App;
use App\Http\Requests\MatKhauFormRequest;
use App\KhoaHoc;
use App\LopHoc;
use App\Services\Library;
use App\TaiKhoan;

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

    public function generateExcelFile(TaiKhoan $taiKhoan, Library $library)
    {
        $taiKhoan = $taiKhoan->locDuLieu()->withTrashed();

        // Generate Data
        $arrRow[] = [
            'Mã Số',
            'Họ và Tên',
            'Tên',
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
            $arrRow[] = [
                $item->id,
                $item->ho_va_ten,
                $item->ten,
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
        $file = \Excel::create('Danh Sach Tai Khoan_' . date('d-m-Y') . '_' . strtotime('now'), function ($excel) use ($arrRow) {
            $excel->sheet(date('d-m-Y'), function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow, null, null, null, false)
                    ->setFreeze('C2');
            });
        })->store('xls', '/tmp', true);

        return response()->json([
            'data' => $file['file'],
        ]);
    }

    public function getDownloadFile($fileName)
    {
        return \Response::download("/tmp/$fileName");
    }
}
