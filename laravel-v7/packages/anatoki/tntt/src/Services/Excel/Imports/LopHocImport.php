<?php

namespace TNTT\Services\Excel\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;

class LopHocImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    protected $data = [];

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * $row = [
     *      0 => "Ngành"
     *      1 => "Cấp"
     *      2 => "Đội"
     *      3 => "Vị Trí Học"
     * ];
     *
     * @param  Collection  $rows
     * @return array
     */
    public function collection(Collection $rows)
    {
        info($rows);
        $khoaId = KhoaHoc::hienTai()->id;
        $lops   = LopHoc::where('khoa_hoc_id', $khoaId)->get();
        $rows   = $rows->whereNotNull('nganh')
            ->whereNotNull('cap')
            ->whereNotNull('doi');

        foreach ($rows as $index => $row) {
            $lop = $lops->filter(function ($lh) use ($row) {
                return $lh->nganh == $row['nganh'] && $lh->cap == $row['cap'] && $lh->doi == $row['doi'];
            })->first();

            if (!$lop) {
                $this->data[] = $row;
            }
        }

        return $this->data;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->data;
    }
}
