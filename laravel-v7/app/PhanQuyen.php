<?php
namespace App;

use Zizaco\Entrust\EntrustPermission;

class PhanQuyen extends EntrustPermission
{
    protected $fillable = [
        'ten_hien_thi',
        'ghi_chu',
    ];

    /**
     * Get the phan_quyen's name. Fix for Entrus
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->ten;
    }

    public function role_nhom()
    {
        $query = $this->roles();
        $query = $query->whereLoai('NHOM');

        return $query;
    }

    public function role_taikhoan()
    {
        $query = $this->roles();
        $query = $query->whereLoai('TAI_KHOAN');

        return $query;
    }
}
