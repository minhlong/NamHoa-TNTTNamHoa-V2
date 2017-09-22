<?php
namespace App\Http\Controllers;

use App\Http\Requests\TaiKhoanFormRequest;
use Illuminate\Support\Facades\App;
use App\Http\Requests\MatKhauFormRequest;
use App\KhoaHoc;
use App\LopHoc;
use App\Services\Library;
use App\TaiKhoan;

class TaiKhoanController extends Controller
{
    /**
     * @param TaiKhoan $taiKhoan
     * @param Library $library
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDanhSach(TaiKhoan $taiKhoan, Library $library)
    {
        $taiKhoan = $taiKhoan->locDuLieu()->withTrashed()->get();

        return response()->json([
            'data' => $taiKhoan,
        ]);
    }
}
