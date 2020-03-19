<?php

use Illuminate\Support\Facades\Route;

// Binding
Route::bind('TaiKhoan', function ($value) {
    return \App\TaiKhoan::withTrashed()->findOrFail($value);
});
Route::bind('LopHoc', function ($value) {
    return \App\LopHoc::findOrFail($value);
});
Route::bind('KhoaHoc', function ($value) {
    return \App\KhoaHoc::findOrFail($value);
});
// Route::bind('PhanQuyen', function ($value) {
//     return \App\PhanQuyen::findOrFail($value);
// });
// Route::bind('NhomTaiKhoan', function ($value) {
//     return \App\NhomTaiKhoan::findOrFail($value);
// });
// Route::bind('ThuMoi', function ($value) {
//     return \App\ThuMoi::findOrFail($value);
// });
// Route::bind('ThietBi', function ($value) {
//     return \App\ThietBi::findOrFail($value);
// });
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

Route::post('dang-nhap', 'DangNhapController@dangNhap');
Route::post('dang-xuat', 'DangNhapController@dangXuat');
Route::post('refresh', 'DangNhapController@refresh');

/* Khoa Hoc */
Route::group(['prefix' => 'khoa-hoc'], function () {
    Route::get(null, 'KhoaHocController@getDanhSach');
    Route::post(null, 'KhoaHocController@postTaoMoi');

    Route::group(['prefix' => '{KhoaHoc}'], function () {
        Route::get(null, 'KhoaHocController@getThongTin');
        Route::post(null, 'KhoaHocController@postThongTin');
    });
});

/* Lop Hoc */
Route::group(['prefix' => 'lop-hoc'], function () {
    Route::get('khoa-{khoaID}', 'LopHocController@getDanhSachTheoKhoa');
    Route::get('{LopHoc}', 'LopHocController@getThongTin');

    // Route::post(null, 'LopHocController@post');
    // Route::post('tap-tin', 'LopHocController@postTapTin')->middleware(['permission:lop-hoc']);
    // Route::post('tap-tin/tao', 'LopHocController@postTao')->middleware(['permission:lop-hoc']);
    // Route::post('{LopHoc}', 'LopHocController@postUpdate')->middleware(['permission:lop-hoc']);
    // Route::post('{LopHoc}/thanh-vien', 'LopHocController@postThemThanhVien')->middleware(['permission:lop-hoc']);
    // Route::post('{LopHoc}/thanh-vien/xoa', 'LopHocController@postXoaThanhVien')->middleware(['permission:lop-hoc']);
    // Route::delete('{LopHoc}', 'LopHocController@postXoa')->middleware(['permission:lop-hoc']);
    //
    // Route::get('{LopHoc}/chuyen-can', 'LopHocController@getChuyenCan');
    // Route::post('{LopHoc}/chuyen-can', 'LopHocController@postChuyenCan'); // Fix: Update permission
    //
    // Route::get('{LopHoc}/hoc-luc', 'LopHocController@getHocLuc');
    // Route::post('{LopHoc}/hoc-luc', 'LopHocController@postHocLuc'); // Fix: Update permission
    //
    // Route::get('{LopHoc}/tong-ket', 'TaiKhoanController@getTongKet');
    // Route::post('{LopHoc}/tong-ket/xep-hang',
    //     'LopHocController@postXepHang')->middleware(['permission:danh-gia-cuoi-nam']);
    // Route::post('{LopHoc}/tong-ket/nhan-xet',
    //     'LopHocController@postNhanXet'); // Fix: Update permission ->middleware(['permission:nhan-xet']);
});