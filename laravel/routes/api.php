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
        
        Route::get('{LopHoc}/hoc-luc', 'LopHocController@getHocLuc');
        Route::post('{LopHoc}/hoc-luc', 'LopHocController@postHocLuc'); // Fix: Update permission

        Route::get('{LopHoc}/tong-ket', 'TaiKhoanController@getTongKet');
        Route::post('{LopHoc}/tong-ket/xep-hang', 'LopHocController@postXepHang')->middleware(['permission:danh-gia-cuoi-nam']);
        Route::post('{LopHoc}/tong-ket/nhan-xet', 'LopHocController@postNhanXet')->middleware(['permission:nhan-xet']);
    });

    /* Khoa Hoc */
    Route::group(['prefix' => 'khoa-hoc'], function () {
        Route::get(null, 'KhoaHocController@getDanhSach');
        Route::post(null, 'KhoaHocController@postTaoMoi')->middleware(['permission:he-thong']);

        Route::group(['prefix' => '{KhoaHoc}'], function () {
            Route::get(null, 'KhoaHocController@getThongTin');
            Route::post(null, 'KhoaHocController@postThongTin')->middleware(['permission:he-thong']);  // Fix: Năm hiện tại hoặc về sau
        });
    });

    /* Phân Quyền */
    Route::group(['prefix' => 'phan-quyen'], function () {
        Route::get(null, 'PhanQuyenController@getDanhSach');
        Route::post('{PhanQuyen}', 'PhanQuyenController@postThongTin');  // Fix: Chỉ mình tài khoảng Hồ Minh Long
        Route::group(['prefix' => '{PhanQuyen}', 'middleware' => ['permission:phan-quyen']], function () {
            Route::post('/nhom', 'PhanQuyenController@postThemNhom');
            Route::post('/tai-khoan', 'PhanQuyenController@postThemTaiKhoan');
            Route::post('/xoa', 'PhanQuyenController@postXoa');
        });
    });

    /* Phân Nhóm */
    Route::group(['prefix' => 'nhom-tai-khoan'], function () {
        Route::get(null, 'NhomTaiKhoanController@get');
        Route::post('{NhomTaiKhoan?}', 'NhomTaiKhoanController@post')->middleware(['permission:phan-quyen']);
        Route::group(['prefix' => '{NhomTaiKhoan}', 'middleware' => ['permission:phan-quyen']], function () {
            Route::post('tai-khoan', 'NhomTaiKhoanController@postThem');
            Route::post('xoa-tai-khoan', 'NhomTaiKhoanController@postXoa');
            Route::delete(null, 'NhomTaiKhoanController@delete');
        });
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
Route::bind('PhanQuyen', function ($value) {
    return \App\PhanQuyen::findOrFail($value);
});
Route::bind('NhomTaiKhoan', function ($value) {
    return \App\NhomTaiKhoan::findOrFail($value);
});