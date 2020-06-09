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

class LopHocInserted implements FromArray, Responsable, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    private $fileName = 'TaoMoi_LopHoc_';
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
                $event->sheet->freezePane('A2');
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Mã Lớp',
            'Tên',
            'Vị Trí Học',
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
                $item['ten'],
                $item['vi_tri_hoc'],
            ];
        }
        return $results;
    }
}
