<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class GiaPha extends Model
{
    public function anh_em()
    {
        return $this->hasMany(TaiKhoan::class);
    }

    public function than_nhan()
    {
        return $this->hasMany(ThanNhan::class);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::setTable('gia_pha');
    }
}
