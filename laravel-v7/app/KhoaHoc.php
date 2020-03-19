<?php

namespace App;

class KhoaHoc extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'so_dot_kiem_tra',
        'so_lan_kiem_tra',
        'ngung_diem_danh',
        'cap_nhat_dot_kiem_tra',
        'xep_hang',
        'xep_loai',
        'di_le',
        'di_hoc',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'xep_hang' => 'array',
        'xep_loai' => 'array',
        'di_le'    => 'array',
        'di_hoc'   => 'array',
    ];
    protected $table = 'khoa_hoc';

    /**
     * @param  null  $date
     * @return mixed
     */
    public static function hienTai($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $KhoaHoc = self::where('id', date('Y', strtotime($date)))
            ->orWhereRaw('(ngay_bat_dau <= ? and ? <= ngay_ket_thuc)', [$date, $date,])
            ->first();

        if (is_object($KhoaHoc)) {
            return $KhoaHoc;
        }

        return self::taoKhoaHocMacDinh($date);
    }

    /**
     * @param $ngayHienTai
     * @return mixed
     */
    private static function taoKhoaHocMacDinh($ngayHienTai)
    {
        $NamHoc     = date('Y', strtotime($ngayHienTai));
        $ngayBatDau = date("$NamHoc-08-01");

        return self::create([
            'id'                    => $NamHoc,
            'ngay_bat_dau'          => $ngayBatDau,
            'ngay_ket_thuc'         => date('Y-m-d', strtotime("$ngayBatDau +1 year -1 day")),
            'so_dot_kiem_tra'       => 5,
            'so_lan_kiem_tra'       => 2,
            'ngung_diem_danh'       => 3,
            'cap_nhat_dot_kiem_tra' => 1,
            'xep_hang'              => [
                'CHUYEN_CAN' => [
                    'LEN_LOP'      => 5,
                    'KHUYEN_KHICH' => 9,
                    'III'          => 8,
                    'II'           => 8,
                    'I'            => 8,
                ],
                'HOC_LUC'    => [
                    'LEN_LOP'      => 5,
                    'KHUYEN_KHICH' => 8,
                    'III'          => 8,
                    'II'           => 8,
                    'I'            => 8,
                ],
                'SO_LUONG'   => [
                    'KHUYEN_KHICH' => 1,
                    'III'          => 1,
                    'II'           => 1,
                    'I'            => 1,
                ],
            ],
            'xep_loai'              => [
                'CHUYEN_CAN' => [
                    'TB'   => 5,
                    'KHA'  => 6.5,
                    'GIOI' => 8,
                ],
                'HOC_LUC'    => [
                    'TB'   => 5,
                    'KHA'  => 6.5,
                    'GIOI' => 8,
                ],
            ],
            'di_le'                 => [
                'K' => -0.5,
                'P' => -0.2,
                'T' => -0.3,
                'H' => -0.2,
            ],
            'di_hoc'                => [
                'K' => -0.5,
                'P' => -0.2,
            ],
        ]);
    }
}
