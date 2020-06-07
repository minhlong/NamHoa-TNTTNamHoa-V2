<?php

namespace TNTT\Repositories;

use TNTT\Models\LopHoc;

class TaiKhoanRepository
{
    public function themThanhVien(LopHoc $lopHoc, $arrID)
    {
        $lopHoc->thanh_vien()->attach($arrID);
        $lopHoc->tinhTongKet();
    }
}