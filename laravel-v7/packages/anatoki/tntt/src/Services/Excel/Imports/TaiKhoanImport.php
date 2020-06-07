<?php

namespace TNTT\Services\Excel\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use TNTT\Exceptions\ExcelInvalidFormat;
use TNTT\Models\KhoaHoc;
use TNTT\Models\LopHoc;
use TNTT\Models\TaiKhoan;
use TNTT\Services\Library;

class TaiKhoanImport implements ToCollection, WithHeadingRow
{
    protected $data = [];

    /**
     * $row = [
     *     0  => "Ngành",
     *     1  => "Cấp",
     *     2  => "Đội",
     *     3  => "Loại Tài Khoản",
     *     4  => "Tên Thánh",
     *     5  => "Họ và Tên",
     *     6  => "Giới Tính",
     *     7  => "Ngày Sinh",
     *     8  => "Ngày Rửa Tội",
     *     9  => "Ngày Ruớc Lễ",
     *     10 => "Ngày Thêm Sức",
     *     11 => "Điện Thoại",
     *     12 => "Địa Chỉ",
     *     13 => null,
     * ];
     *
     * @param  Collection  $rows
     * @return TaiKhoan[]
     * @throws ExcelInvalidFormat
     */
    public function collection(Collection $rows)
    {
        $khoaId    = KhoaHoc::hienTai()->id;
        $lopHocArr = LopHoc::where('khoa_hoc_id', $khoaId)->get();

        $library = new Library();
        $rows    = $rows->whereNotNull('ngay_sinh')->whereNotNull('ho_va_ten')
            ->map(function ($c) use ($library) {
                $c['ngay_sinh']     = $library->chuanHoaNgay($c['ngay_sinh']);
                $c['ngay_rua_toi']  = $c['ngay_rua_toi'] ? $library->chuanHoaNgay($c['ngay_rua_toi']) : null;
                $c['ngay_ruoc_le']  = $c['ngay_ruoc_le'] ? $library->chuanHoaNgay($c['ngay_ruoc_le']) : null;
                $c['ngay_them_suc'] = $c['ngay_them_suc'] ? $library->chuanHoaNgay($c['ngay_them_suc']) : null;

                return $c;
            });

        $tmpRule = [
            'ho_va_ten'     => 'required',
            'ngay_sinh'     => 'required|date_format:Y-m-d',
            'ngay_rua_toi'  => 'nullable|date_format:Y-m-d',
            'ngay_ruoc_le'  => 'nullable|date_format:Y-m-d',
            'ngay_them_suc' => 'nullable|date_format:Y-m-d',
        ];

        foreach ($rows as $index => $row) {
            $validator = Validator::make($row->toArray(), $tmpRule);
            if ($validator->fails()) {
                throw  new ExcelInvalidFormat([
                    'error' => $validator->errors(),
                    'item'  => $row,
                ]);
            }

            $lop = $lopHocArr->filter(function ($lh) use ($row) {
                return $lh->nganh == $row['nganh'] && $lh->cap == $row['cap'] && $lh->doi == $row['doi'];
            })->first();

            if ($lop) {
                $row = array_merge($row->toArray(), [
                    'lop_hoc_id'  => $lop->id,
                    'lop_hoc_ten' => $lop->taoTen(),
                ]);
            }

            $this->data[] = $row;
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