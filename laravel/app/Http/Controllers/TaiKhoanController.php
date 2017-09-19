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
    private $tmpPath = '/tmp';

    /**
     * Lay Danh Sach Loai Tai Khoan.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLoaiTaiKhoan()
    {
        return response()->json(TaiKhoan::$loaiTaiKhoan);
    }

    /**
     * Lay Danh Sach Loai Trang Thai.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLoaiTrangThai()
    {
        return response()->json(TaiKhoan::$loaiTrangThai);
    }

    private function generateExcelFile($taiKhoan, Library $library)
    {
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
        $file = \Excel::create(strtotime('now'), function ($excel) use ($arrRow) {
            $excel->sheet(date('d-m-Y'), function ($sheet) use ($arrRow) {
                $sheet->fromArray($arrRow, null, null, null, false)
                    ->setFreeze('C2');
            });
        })->store('xls', $this->tmpPath, true);

        return $file['file'];
    }

    public function getDownloadFile($fileName)
    {
        return \Response::download($this->tmpPath . "/$fileName");
    }

    /**
     * @param TaiKhoan $taiKhoan
     * @param Library $library
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDanhSach(TaiKhoan $taiKhoan, Library $library)
    {
        return response()->json([
        	'data' => $taiKhoan->all(),
        ]);

        $records = [
            'data' => [],
            'draw' => \Request::get('draw'),
        ];
        switch (\Request::has('customActionType')) {
            case 'tai_khoan':
                $arrTmpTaiKhoanID = \Request::get('checked_id');
                switch ($customActionName = \Request::get('customActionName')) {
                    case 'xuat_du_lieu':
                        $tmpTaiKhoan = TaiKhoan::whereIn('id', $arrTmpTaiKhoanID);
                        $fileName = $this->generateExcelFile($tmpTaiKhoan, $library);
                        $url = action('TaiKhoanController@getDownloadFile', $fileName);
                        $records['customActionStatus'] = 'OK'; // pass custom message(useful for getting status of group actions)
                        $records['customActionMessage'] = '<strong>Đã tạo Tập Tin!</strong>  Click <a class="bold" href="' . $url . '" target="_blank">vào đây</a> để tải tập tin về!';
                        break;
                    case 'ngay_ruoc_le':
                    case 'ngay_them_suc':
                        TaiKhoan::whereIn('id', $arrTmpTaiKhoanID)->update([
                            $customActionName => $library->chuanHoaNgay(\Request::get('date')),
                        ]);
                        $records['customActionStatus'] = 'OK'; // pass custom message(useful for getting status of group actions)
                        $records['customActionMessage'] = 'Đã cập nhật!';
                        break;
                }
                break;
        }
        $taiKhoan = $taiKhoan->locDuLieu($library);
        $records['iTotalDisplayRecords'] = $taiKhoan->get()->count();
        if (($length = \Request::get('length')) > 0) {
            $taiKhoan = $taiKhoan->skip(\Request::get('start'))->take($length);
        }
        foreach ($taiKhoan->get() as $thongTin) {
            // If you want to change response fields, please make sure which not effect to Client Js (TaiKhoan.thamSoTimKiem)
            $records['data'][] = [
                'id'         => $thongTin->id,
                'ten_thanh'  => $thongTin->ten_thanh, // Use for add tags
                'ho_va_ten'  => $thongTin->ho_va_ten,
                'dien_thoai' => $thongTin->dien_thoai,
                'ngay_sinh'  => $library->chuanHoaNgay($thongTin->ngay_sinh),
                'ngay_tao'   => $library->chuanHoaNgay(substr($thongTin->created_at, 0, 10)),
            ];
        }

        return response()->json($records);
    }

    /**
     * Lấy Thông Tin Cá Nhân.
     * @param TaiKhoan $taiKhoan
     * @return mixed
     */
    public function getThongTin(TaiKhoan $taiKhoan)
    {
        $taiKhoan->load(['lop_hoc', 'than_nhan', 'anh_em']);
        foreach ($taiKhoan->lop_hoc as &$item) {
            $item->ten_lop = $item->taoTen();
        }

        return response()->json($taiKhoan->toArray());
    }

    /**
     * Luu Thong Tin Tai Khoan.
     * @param TaiKhoan $taiKhoan
     * @param TaiKhoanFormRequest $taiKhoanFormRequest
     * @return string
     */
    public function postThongTin(TaiKhoan $taiKhoan, TaiKhoanFormRequest $taiKhoanFormRequest)
    {
        if (!$taiKhoan->id) {
            $taiKhoan = $taiKhoan->taoTaiKhoan($taiKhoanFormRequest->all());
            // Insert via CSV Format
            if ($taiKhoanFormRequest->get('ho_va_ten_cha') || $taiKhoanFormRequest->get('ho_va_ten_me')) {
                if ($taiKhoanFormRequest->get('nganh')) {
                    $lopHoc = new LopHoc();
                    $lopHoc = $lopHoc->locDuLieu();
                    $lopHoc = $lopHoc->where('khoa_hoc_id', KhoaHoc::hienTaiHoacTaoMoi()->id)
                        ->first();
                    if ($lopHoc) {
                        App::make('App\Http\Controllers\LopHocController')->postHocVien($lopHoc, $taiKhoan);
                    }
                }
                if ($taiKhoanFormRequest->get('ho_va_ten_cha')) {
                    $taiKhoan->luuThanNhan([
                        'loai_quan_he' => 'CHA',
                        'ho_va_ten'    => $taiKhoanFormRequest->get('ho_va_ten_cha'),
                    ]);
                }
                if ($taiKhoanFormRequest->get('ho_va_ten_me')) {
                    $taiKhoan->luuThanNhan([
                        'loai_quan_he' => 'ME',
                        'ho_va_ten'    => $taiKhoanFormRequest->get('ho_va_ten_me'),
                    ]);
                }

                return $taiKhoan->toJson();
            }
        } else {
            $taiKhoan->fill($taiKhoanFormRequest->all());
            $taiKhoan->save();
            // Update Trang Thai
            if ($taiKhoan->trang_thai == 'TAM_NGUNG' && !$taiKhoan->trashed()) {
                $taiKhoan->delete();
            } elseif ($taiKhoan->trang_thai == 'HOAT_DONG' && $taiKhoan->trashed()) {
                $taiKhoan->restore();
            }
        }

        return $this->getThongTin($taiKhoan);
    }

    /**
     * @param TaiKhoan $taiKhoan
     * @param MatKhauFormRequest $matKhauRequest
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postLuuMatKhau(TaiKhoan $taiKhoan, MatKhauFormRequest $matKhauRequest)
    {
        if (\Hash::check($matKhauRequest->get('mat_khau_hien_tai'), $taiKhoan->mat_khau)) {
            $taiKhoan->capNhatMatKhau($matKhauRequest->get('mat_khau_moi'));
            $taiKhoan->save();

            return response()->json(['msg' => 'Succ']);
        }

        abort(422, 'Dữ liệu không hợp lệ!');
    }

    /**
     * @param TaiKhoan $taiKhoan
     * @param Library $library
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postLuuHinhAnhDaiDien(TaiKhoan $taiKhoan, Library $library)
    {
        $imageData = \Request::get('avarImg');
        $imagePath = $library->getProfilePath() . "/$taiKhoan->id.png";
        $library->base64ToImage($imageData, $imagePath);

        return response()->json([
            'msg'     => 'Succ',
            'avarImg' => $imageData,
        ]);
    }

    /**
     * @param TaiKhoan $taiKhoan
     * @param Library $library
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getHinhAnhDaiDien(TaiKhoan $taiKhoan, Library $library)
    {
        $response = response($library->getProfileImage($taiKhoan->id));
        $response->header('Content-Type', 'image/png');

        return $response;
    }

    public function getHinhSoGDCG(TaiKhoan $taiKhoan)
    {
        $dirPath = env('GDCG_IMAGES_PATH');
        $imagePath = "{$dirPath}/{$taiKhoan->id}.png";
        if (!file_exists($imagePath)) {
            $imagePath = "{$dirPath}/default.png";
        }
        $response = response(\File::get($imagePath));
        $response->header('Content-Type', 'image/png');

        return $response;
    }

    public function postAnhEm(TaiKhoan $taiKhoan)
    {
        if ($arrNewAnhEm = \Request::get('anh_em')) {
            $arrNewAnhEm = array_pluck($arrNewAnhEm, 'id');
        }
        $taiKhoan->luuAnhEm($arrNewAnhEm);

        return $this->getThongTin($taiKhoan);
    }

    /**
     * Thêm Thân Nhân.
     * @param TaiKhoan $taiKhoan
     * @return string
     */
    public function postLuuThanNhan(TaiKhoan $taiKhoan)
    {
        return $taiKhoan->luuThanNhan(\Request::all())->toJson();
    }

    public function postResetMatKhau(TaiKhoan $taiKhoan)
    {
        $taiKhoan->capNhatMatKhau(\Request::get('mat_khau'));
        $taiKhoan->save();

        return response()->json($taiKhoan);
    }
}
