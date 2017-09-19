<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ThanNhan extends Model
{
    protected $fillable = [
        'loai_quan_he',
        'ho_va_ten',
        'dien_thoai',
        'ghi_chu',
        'gia_pha_id',
    ];
    public static $loaiQuanHe = [
        'CHA',
        'ME',
        'LOAI_KHAC',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('than_nhan');
    }

    public function gia_pha()
    {
        return $this->belongsTo(GiaPha::class);
    }
}
