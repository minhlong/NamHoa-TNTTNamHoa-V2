<?php
namespace App;

use Zizaco\Entrust\EntrustRole;

class NhomTaiKhoan extends EntrustRole
{
    public function tai_khoan()
    {
        return $this->users();
    }

    public function phan_quyen()
    {
        return $this->perms();
    }

    /**
     * @var array
     */
    protected $fillable = [
        'loai',
        'ten',
        'ten_hien_thi',
    ];
}
