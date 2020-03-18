<?php

namespace App\Http\Controllers;

use App\KhoaHoc;

class KhoaHocController extends Controller
{
    public function getDanhSach()
    {
        return response()->json([
            'data' => KhoaHoc::all(),
        ]);
    }
}
