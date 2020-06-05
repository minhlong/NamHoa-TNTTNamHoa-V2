<?php

namespace TNTT\Services\Excel\Exports;

use App\DiemDanh;
use App\DiemSo;
use App\KhoaHoc;
use App\LopHoc;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Request;

class TongKetSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithEvents
{
    private $lopHocs;
    private $data;
    private $khoaID = null;

    /**
     *  Nếu không có lớp cụ thể, sẽ export toàn bộ học viên của khóa hiện tại
     * @param  LopHoc|null  $lopHoc
     */
    public function __construct(LopHoc $lopHoc = null)
    {
        $this->lopHocs = collect();

        if ($lopHoc && $lopHoc->id) {
            $this->khoaID    = $lopHoc->khoa_hoc_id;
            $this->lopHocs[] = $lopHoc;
        } else {
            if (!Request::has('khoa')) {
                abort(400, 'Phải chọn 1 khoá học cụ thể.');
            }
            $this->khoaID  = Request::get('khoa');
            $this->lopHocs = LopHoc::locDuLieu()->get();
        }

        $this->data = $this->getData();
    }

    public function title(): string
    {
        return 'Tong Ket - Khoa '.$this->khoaID;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->freezePane('D3');
            },
        ];
    }

    public function headings(): array
    {
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

        foreach ($this->data['SoDot'] as $dot) {
            foreach ($this->data['SoLan'] as $lan) {
                $arrHeaderLine1[] = "KT Lần $dot";
                $arrHeaderLine2[] = "Đợt $lan";
            }
        }

        foreach ($this->data['DiemDanh'] as $ngay => $item) {
            $arrHeaderLine1[] = $ngay;
            $arrHeaderLine1[] = $ngay;
            $arrHeaderLine2[] = 'Đi Lễ';
            $arrHeaderLine2[] = 'Đi Học';
        }

        return [
            $arrHeaderLine1,
            $arrHeaderLine2,
        ];
    }

    public function array(): array
    {
        $arrRow = [];
        foreach ($this->data['Data'] as $item) {
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
        foreach ($this->data['SoDot'] as $dot) {
            foreach ($this->data['SoLan'] as $lan) {
                foreach ($arrRow as $id => &$info) {
                    $info["Diem | Dot $dot - Lan $lan"] = isset($this->data['DiemSo'][$id][$dot][$lan]) ? $this->data['DiemSo'][$id][$dot][$lan] : null;
                }
            }
        }
        foreach ($this->data['DiemDanh'] as $ngay => $item) {
            foreach ($arrRow as $id => &$info) {
                $info[$ngay.' - Di Le']  = isset($item[$id]['di_le']) ? $item[$id]['di_le'] : null;
                $info[$ngay.' - Di Hoc'] = isset($item[$id]['di_hoc']) ? $item[$id]['di_hoc'] : null;
            }
        }
        return $arrRow;
    }

    public function getData()
    {
        $arrHocVien = collect();
        $khoaHoc    = KhoaHoc::findOrFail($this->khoaID);
        $res        = [
            'Data'     => [],
            'DiemDanh' => [],
            'DiemSo'   => [],
            'SoDot'    => [],
            'SoLan'    => [],
        ];

        foreach ($this->lopHocs as $lopHoc) {
            $arrTmp = $lopHoc->hoc_vien()->locDuLieu()->get();
            $tenLop = $lopHoc->taoTen(true);
            foreach ($arrTmp as &$hocVien) {
                $hocVien->pivot->tenLop = $tenLop;
                $arrHocVien[]           = $hocVien;
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
                $hocVien->pivot->chuyen_can = round($hocVien->pivot->chuyen_can, 2);
                $hocVien->pivot->hoc_luc    = round($hocVien->pivot->hoc_luc, 2);
                $hocVien->pivot->tb_canam   = round(($hocVien->pivot->chuyen_can + $hocVien->pivot->hoc_luc) / 2, 2);
            }
        }
        $res['Data'] = $arrHocVien->toArray();

        // Add Diem Danh
        $arrDiemDanh = DiemDanh::whereIn('tai_khoan_id',
            $arrHocVien->pluck('id'))
            ->whereBetween('ngay',
                [$khoaHoc->ngay_bat_dau, $khoaHoc->ngay_ket_thuc])
            ->whereNull('phan_loai')
            ->orderBy('ngay')
            ->get();
        foreach ($arrDiemDanh as $item) {
            $res['DiemDanh'][$item->ngay] [$item->tai_khoan_id] = [
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
            $res['DiemSo'] [$item->tai_khoan_id] [$item->dot] [$item->lan] = $item->diem;
            $res['SoDot'][$item->dot]                                      = $item->dot;
            $res['SoLan'][$item->lan]                                      = $item->lan;
        }
        $res['SoDot'] = array_values($res['SoDot']);
        $res['SoLan'] = array_values($res['SoLan']);

        return $res;
    }
}
