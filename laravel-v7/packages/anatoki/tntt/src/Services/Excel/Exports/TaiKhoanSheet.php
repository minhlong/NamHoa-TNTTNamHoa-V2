<?php

namespace TNTT\Services\Excel\Exports;

use TNTT\Models\TaiKhoan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Request;

class TaiKhoanSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    private $khoaID = null;

    public function __construct()
    {
        $this->khoaID = Request::get('khoa');
    }

    public function title(): string
    {
        return 'Danh Sach';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->freezePane('C2');
            },
        ];
    }

    public function headings(): array
    {
        return [
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
    }

    /**
     * @param  TaiKhoan  $data
     * @return array
     */
    public function map($data): array
    {
        $lop = null;
        if ($this->khoaID && $data->lop_hoc->first()) {
            $lop = $data->lop_hoc->first()->taoTen();
        }

        return [
            $data->id,
            $data->ho_va_ten,
            $data->ten,
            $lop,
            $data->loai_tai_khoan,
            $data->trang_thai,
            $data->ten_thanh,
            $data->gioi_tinh,
            $data->ngay_sinh,
            $data->ngay_rua_toi,
            $data->ngay_ruoc_le,
            $data->ngay_them_suc,
            $data->email,
            $data->dien_thoai,
            $data->dia_chi,
            $data->giao_ho,
            $data->ghi_chu,
        ];
    }

    public function collection()
    {
        /** @var TaiKhoan $taiKhoan */
        $taiKhoan = TaiKhoan::locDuLieu()->withTrashed();

        if ($khoaID = $this->khoaID) {
            $taiKhoan->with([
                'lop_hoc' => function ($q) use ($khoaID) {
                    $q->locDuLieu();
                },
            ]);
        }

        return $taiKhoan->get();
    }
}
