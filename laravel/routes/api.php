<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('dang-nhap', 'DangNhapController@postDangNhap');

Route::group(['middleware' => 'auth-jwt'], function () {
    /* Trang Chu */
    Route::get('trang-chu', 'TrangChuController@getThongTin');

    /* Tai Khoan */
    Route::group(['prefix' => 'tai-khoan'], function () {
        Route::get(null, 'TaiKhoanController@getDanhSach');
        Route::get('export', 'TaiKhoanController@generateExcelFile');
        Route::post('tap-tin', 'TaiKhoanController@postTapTin');
        Route::post('tap-tin/tao', 'TaiKhoanController@postTao');
        Route::get('{TaiKhoan}', 'TaiKhoanController@getThongTin');
        Route::post('{TaiKhoan}', 'TaiKhoanController@postUpdate');
        Route::post('{TaiKhoan}/mat-khau', 'TaiKhoanController@postMatKhau');
        Route::post('{TaiKhoan}/xoa', 'TaiKhoanController@postXoa')->middleware(['permission:tai-khoan']);
    });

    /* Lop Hoc */
    Route::get('lop-hoc', function () {
        return response()->json([
            'data' => \App\LopHoc::all(),
        ]);
    });
    Route::get('lop-hoc/{LopHoc}', function ($obj) {
        return response()->json([
            'data' => $obj,
        ]);
    });

    /* Khoa Hoc */
    Route::get('khoa-hoc', function () {
        return response()->json([
            'data' => \App\KhoaHoc::all(),
        ]);
    })->middleware(['permission:tai-khoan']);
    Route::get('khoa-hoc/{KhoaHoc}', function ($obj) {
        return response()->json([
            'data' => $obj,
        ]);
    });
});

Route::bind('TaiKhoan', function ($value) {
    return \App\TaiKhoan::withTrashed()->findOrFail($value);
});
Route::bind('LopHoc', function ($value) {
    return \App\LopHoc::findOrFail($value);
});
Route::bind('KhoaHoc', function ($value) {
    return \App\KhoaHoc::findOrFail($value);
});