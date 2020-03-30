<?php

use Illuminate\Support\Facades\Route;

// Binding
Route::bind('TaiKhoan', function ($value) {
    return \App\TaiKhoan::withTrashed()->findOrFail($value);
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

// Auto pull git when push new commits
Route::post('auto-pull', 'PullGitController@postAutoPull');

/* Trang Chu */
Route::get('trang-chu', 'TrangChuController@getThongTin');

/* Tai Khoan */
Route::group(['prefix' => 'tai-khoan'], function () {
    Route::get(null, 'TaiKhoanController@getDanhSach');
    Route::get('export', 'TaiKhoanController@generateExcelFile');
    // Route::post('ngay-them-suc', 'TaiKhoanController@postThemSuc')->middleware(['permission:Lớp Học']);
    // Route::post('ngay-ruoc-le', 'TaiKhoanController@postRuocLe')->middleware(['permission:Lớp Học']);
    //
    // Route::post('tap-tin', 'TaiKhoanController@postTapTin')->middleware(['permission:Tài Khoản']);
    // Route::post('tap-tin/tao', 'TaiKhoanController@postTao')->middleware(['permission:Tài Khoản']);
    //
    Route::get('{TaiKhoan}', 'TaiKhoanController@getThongTin');
    // Route::post('{TaiKhoan}', 'TaiKhoanController@postUpdate');
    // Route::delete('{TaiKhoan}', 'TaiKhoanController@postXoa')->middleware(['permission:Tài Khoản']);
    // Route::post('{TaiKhoan}/mat-khau', 'TaiKhoanController@postMatKhau');
});

/* Phân Quyền */
Route::group(['prefix' => 'phan-quyen'], function () {
    Route::get(null, 'PhanQuyenController@getDanhSach');
    // Route::post('{PhanQuyen}', 'PhanQuyenController@postThongTin');
    // Route::group(['prefix' => '{PhanQuyen}', 'middleware' => ['permission:Phân Quyền']], function () {
    //     Route::post('/nhom', 'PhanQuyenController@postThemNhom');
    //     Route::post('/tai-khoan', 'PhanQuyenController@postThemTaiKhoan');
    //     Route::post('/xoa', 'PhanQuyenController@postXoa');
    // });
});

/* Phân Nhóm */
Route::group(['prefix' => 'nhom-tai-khoan'], function () {
    Route::get(null, 'NhomTaiKhoanController@get');
//     Route::post('{NhomTaiKhoan?}', 'NhomTaiKhoanController@post')->middleware(['permission:Phân Quyền']);
//     Route::group(['prefix' => '{NhomTaiKhoan}', 'middleware' => ['permission:Phân Quyền']], function () {
//         Route::post('tai-khoan', 'NhomTaiKhoanController@postThem');
//         Route::post('xoa-tai-khoan', 'NhomTaiKhoanController@postXoa');
//         Route::delete(null, 'NhomTaiKhoanController@delete');
//     });
});

/* Khoa Hoc */
Route::group(['prefix' => 'khoa-hoc'], function () {
    Route::get(null, 'KhoaHocController@getDanhSach');
    Route::post(null, 'KhoaHocController@postTaoMoi')->middleware(['permission:Hệ Thống']);

    Route::group(['prefix' => '{khoaHoc}'], function () {
        Route::get(null, 'KhoaHocController@getThongTin');
        Route::post(null, 'KhoaHocController@postThongTin')->middleware(['permission:Hệ Thống']);
    });
});

/* Lop Hoc */
Route::group(['prefix' => 'lop-hoc'], function () {
    Route::get('khoa-{khoaID}', 'LopHocController@getDanhSachTheoKhoa');
    Route::get('{lopHoc}', 'LopHocController@getThongTin');

    // Route::post(null, 'LopHocController@post');
    // Route::post('tap-tin', 'LopHocController@postTapTin')->middleware(['permission:Lớp Học']);
    // Route::post('tap-tin/tao', 'LopHocController@postTao')->middleware(['permission:Lớp Học']);
    // Route::post('{LopHoc}', 'LopHocController@postUpdate')->middleware(['permission:Lớp Học']);
    // Route::post('{LopHoc}/thanh-vien', 'LopHocController@postThemThanhVien')->middleware(['permission:Lớp Học']);
    // Route::post('{LopHoc}/thanh-vien/xoa', 'LopHocController@postXoaThanhVien')->middleware(['permission:Lớp Học']);
    // Route::delete('{LopHoc}', 'LopHocController@postXoa')->middleware(['permission:Lớp Học']);
    //
    // Route::get('{LopHoc}/chuyen-can', 'LopHocController@getChuyenCan');
    // Route::post('{LopHoc}/chuyen-can', 'LopHocController@postChuyenCan'); // Fix: Update permission
    //
    // Route::get('{LopHoc}/hoc-luc', 'LopHocController@getHocLuc');
    // Route::post('{LopHoc}/hoc-luc', 'LopHocController@postHocLuc'); // Fix: Update permission
    //
    Route::get('{lopHoc}/tong-ket', 'LopHocController@getTongKet');
    // Route::post('{LopHoc}/tong-ket/xep-hang',
    //     'LopHocController@postXepHang')->middleware(['permission:danh-gia-cuoi-nam']);
    // Route::post('{LopHoc}/tong-ket/nhan-xet',
    //     'LopHocController@postNhanXet'); // Fix: Update permission ->middleware(['permission:nhan-xet']);
});