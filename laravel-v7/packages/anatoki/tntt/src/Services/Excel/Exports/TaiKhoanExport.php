<?php

namespace TNTT\Services\Excel\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Excel;
use Request;

class TaiKhoanExport implements WithMultipleSheets, Responsable
{
    use Exportable;

    private $fileName = 'DanhSachTaiKhoan_';
    private $writerType = Excel::XLSX;

    public function __construct()
    {
        $this->fileName .= Carbon::now()->format('d-m-Y').'.xlsx';
    }

    public function sheets(): array
    {
        $sheets = [
            new TaiKhoanSheet(),
        ];

        if (Request::get('khoa')) {
            $sheets[] = new TongKetSheet();
        }

        return $sheets;
    }
}
