<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api', 'namespace' => 'TNTT\Controllers'], function () {

    // Auto pull git when push new commits
    Route::post('auto-pull', 'PullGitController@postAutoPull');

    /* Trang Chu */
    Route::get('trang-chu', 'TrangChuController@get');

    Route::post('auth/login', 'DangNhapController@login');
    Route::post('auth/logout', 'DangNhapController@logout');
    Route::post('auth/refresh', 'DangNhapController@refresh');

    /* Tai Khoan */
    Route::group(['prefix' => 'tai-khoan'], function () {
        Route::get(null, 'TaiKhoanController@index');
        Route::get('export', 'TaiKhoanController@export');
        Route::put('ngay-them-suc', 'TaiKhoanController@updateThemSuc');
        Route::put('ngay-ruoc-le', 'TaiKhoanController@updateRuocLe');

        Route::post('import/upload', 'TaiKhoanController@importStep1');
        Route::post('import/insert', 'TaiKhoanController@importStep2');
        Route::get('import/file', 'TaiKhoanController@importStep3');

        Route::get('{taiKhoan}', 'TaiKhoanController@show');
        Route::put('{taiKhoan}', 'TaiKhoanController@update');
        Route::put('{taiKhoan}/mat-khau', 'TaiKhoanController@updatePassword');
        Route::delete('{taiKhoan}', 'TaiKhoanController@delete');
    });

    /* Lop Hoc */
    Route::group(['prefix' => 'lop-hoc'], function () {
        Route::get('khoa-{khoaID}', 'LopHocController@index');

        Route::post('import/upload', 'LopHocController@importStep1');
        Route::post('import/insert', 'LopHocController@importStep2');
        Route::get('import/file', 'LopHocController@importStep3');

        Route::get('{lopHoc}', 'LopHocController@show');
        Route::put('{lopHoc}', 'LopHocController@update');
        Route::delete('{lopHoc}', 'LopHocController@delete');

        Route::post('{lopHoc}/thanh-vien', 'LopHocController@postMember');
        Route::delete('{lopHoc}/thanh-vien', 'LopHocController@deleteMember');

        Route::get('{lopHoc}/chuyen-can', 'LopHocController@getChuyenCan');
        Route::post('{lopHoc}/chuyen-can', 'LopHocController@postChuyenCan'); // Fix: Update permission

        Route::get('{lopHoc}/hoc-luc', 'LopHocController@getHocLuc');
        Route::post('{lopHoc}/hoc-luc', 'LopHocController@postHocLuc');

        Route::get('{lopHoc}/tong-ket', 'LopHocController@getTongKet');
        Route::post('{lopHoc}/tong-ket/xep-hang', 'LopHocController@postXepHang');
        Route::post('{lopHoc}/tong-ket/nhan-xet', 'LopHocController@postNhanXet');
    });

    /* Khoa Hoc */
    Route::group(['prefix' => 'khoa-hoc'], function () {
        Route::get(null, 'KhoaHocController@getDanhSach');
        // Route::post(null, 'KhoaHocController@postTaoMoi')->middleware(['permission:Hệ Thống']);

        // Route::get('{khoaHoc}', 'KhoaHocController@getThongTin');
        // Route::post(null, 'KhoaHocController@postThongTin')->middleware(['permission:Hệ Thống']);
    });

    Route::apiResources([
        'phan-quyen'     => 'PhanQuyenController',
        'nhom-tai-khoan' => 'PhanNhomController',
    ], ['except' => ['store', 'destroy']]);
});