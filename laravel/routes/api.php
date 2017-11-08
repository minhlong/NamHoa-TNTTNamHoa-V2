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

        Route::post('tap-tin', 'TaiKhoanController@postTapTin')->middleware(['permission:tai-khoan']);
        Route::post('tap-tin/tao', 'TaiKhoanController@postTao')->middleware(['permission:tai-khoan']);

        Route::get('{TaiKhoan}', 'TaiKhoanController@getThongTin');
        Route::post('{TaiKhoan}', 'TaiKhoanController@postUpdate');
        Route::delete('{TaiKhoan}', 'TaiKhoanController@postXoa')->middleware(['permission:tai-khoan']);
        Route::post('{TaiKhoan}/mat-khau', 'TaiKhoanController@postMatKhau');
    });

    /* Lop Hoc */
    Route::group(['prefix' => 'lop-hoc'], function () {
        Route::get('khoa-{khoaID}', 'LopHocController@getDanhSachTheoKhoa');
        Route::get('{LopHoc}', 'LopHocController@getThongTin');

        Route::post(null, 'LopHocController@post');
        Route::post('tap-tin', 'LopHocController@postTapTin')->middleware(['permission:lop-hoc']);
        Route::post('tap-tin/tao', 'LopHocController@postTao')->middleware(['permission:lop-hoc']);
        Route::post('{LopHoc}', 'LopHocController@postUpdate')->middleware(['permission:lop-hoc']);
        Route::post('{LopHoc}/thanh-vien', 'LopHocController@postThemThanhVien')->middleware(['permission:lop-hoc']);
        Route::post('{LopHoc}/thanh-vien/xoa', 'LopHocController@postXoaThanhVien')->middleware(['permission:lop-hoc']);
        Route::delete('{LopHoc}', 'LopHocController@postXoa')->middleware(['permission:lop-hoc']);
        
        Route::get('{LopHoc}/chuyen-can', 'LopHocController@getChuyenCan');
        Route::post('{LopHoc}/chuyen-can', 'LopHocController@postChuyenCan'); // Fix: Update permission
    });

    /* Khoa Hoc */
    Route::get('khoa-hoc', function () {
        return response()->json([
            'data' => \App\KhoaHoc::all(),
        ]);
    });
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