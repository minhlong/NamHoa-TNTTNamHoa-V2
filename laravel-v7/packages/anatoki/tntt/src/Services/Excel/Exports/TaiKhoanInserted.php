<?php

namespace TNTT\Services\Excel\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;

class TaiKhoanInserted implements FromArray, Responsable, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    private $fileName = 'TaoMoi_TaiKhoan_';
    private $writerType = Excel::XLSX;
    private $data = null;

    public function __construct($data)
    {
        $this->data     = $data;
        $this->fileName .= Carbon::now()->format('d-m-Y_h-i-s').'.xlsx';
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
            'Điện Thoại',
            'Địa Chỉ',
        ];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $results = [];
        foreach ($this->data as $item) {
            $results[] = [
                $item['id'],
                $item['ho_va_ten'],
                $item['ten'],
                $item['lop_hoc_ten'],
                $item['loai_tai_khoan'],
                $item['trang_thai'],
                $item['ten_thanh'],
                $item['gioi_tinh'],
                mapDate($item['ngay_sinh']),
                mapDate($item['ngay_rua_toi']),
                mapDate($item['ngay_ruoc_le']),
                mapDate($item['ngay_them_suc']),
                $item['dien_thoai'],
                $item['dia_chi'],
            ];
        }
        return $results;
    }
}
