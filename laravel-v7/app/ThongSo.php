<?php

namespace App;

class ThongSo extends BaseModel
{
    protected $table = 'thong_so';
    protected $fillable = [
        'trang_thai',
        'ky_hieu',
        'ten',
        'thu_tu',
    ];
    public static $loaiTrangThai = [
        'HIEN_THI',
        'KHONG_HIEN_THI',
    ];

    /**
     * @param $kyHieu
     * @return mixed
     */
    public static function layTenNganh($kyHieu)
    {
        return self::layTen('NGANH', $kyHieu);
    }

    /**
     * @param $kyHieu
     * @return mixed
     */
    public static function layTenCap($kyHieu)
    {
        return self::layTen('CAP', $kyHieu);
    }

    /**
     * @param $kyHieu
     * @return mixed
     */
    public static function layTenDoi($kyHieu)
    {
        return self::layTen('DOI', $kyHieu);
    }

    /**
     * @param $loaiDuLieu
     * @param $kyHieu
     * @return mixed
     */
    public static function layTen($loaiDuLieu, $kyHieu)
    {
        return self::where('loai_du_lieu', $loaiDuLieu)
            ->where('ky_hieu', $kyHieu)
            ->first()->ten;
    }
}
