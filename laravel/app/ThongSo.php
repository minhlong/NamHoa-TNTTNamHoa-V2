<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ThongSo extends Model
{
    protected $fillable = [
        'trang_thai',
        'ky_hieu',
        'ten',
        'thu_tu',
    ];
    /**
     * @var array
     */
    public static $loaiTrangThai = [
        'HIEN_THI',
        'KHONG_HIEN_THI',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('thong_so');
    }

    /**
     * @return array
     */
    public function getTrangThai()
    {
        return self::$loaiTrangThai;
    }

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
