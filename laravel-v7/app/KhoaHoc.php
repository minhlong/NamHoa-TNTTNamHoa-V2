<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KhoaHoc extends Model
{
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

    protected $casts = [
        'xep_hang' => 'array',
        'xep_loai' => 'array',
        'di_le'    => 'array',
        'di_hoc'   => 'array',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('khoa_hoc');
    }

    /**
     * @param null $ngayHienTai
     * @return static
     */
    public static function hienTaiHoacTaoMoi($ngayHienTai = null)
    {
        if (!$ngayHienTai) {
            $ngayHienTai = date('Y-m-d');
        }
        $KhoaHoc = self::where('id', date('Y', strtotime($ngayHienTai)))
            ->orWhereRaw('(ngay_bat_dau <= ? and ? <= ngay_ket_thuc)', [
                $ngayHienTai,
                $ngayHienTai,
            ])->first();
        if (is_object($KhoaHoc)) {
            return $KhoaHoc;
        }

        return self::taoKhoaHocMacDinh($ngayHienTai);
    }

    /**
     * @param $ngayHienTai
     * @return static
     */
    private static function taoKhoaHocMacDinh($ngayHienTai)
    {
        $NamHoc     = date('Y', strtotime($ngayHienTai));
        $ngayBatDau = date("$NamHoc-08-01");
        // if ($ngayHienTai < $ngayBatDau) {
        //     --$NamHoc;
        //     $ngayBatDau = date("$NamHoc-m-d", strtotime($ngayBatDau));
        // }
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
