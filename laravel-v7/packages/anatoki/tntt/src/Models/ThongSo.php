<?php

namespace TNTT\Models;

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

    public static function layTenNganh($kyHieu)
    {
        return self::layTen('NGANH', $kyHieu);
    }

    public static function layTenCap($kyHieu)
    {
        return self::layTen('CAP', $kyHieu);
    }

    public static function layTenDoi($kyHieu)
    {
        return self::layTen('DOI', $kyHieu);
    }

    public static function layTen($loaiDuLieu, $kyHieu)
    {
        info($loaiDuLieu);
        info( $kyHieu);
        $x= self::where('loai_du_lieu', $loaiDuLieu)
            ->where('ky_hieu', $kyHieu)
            ->first()->ten;
        info($x);
    }
}
