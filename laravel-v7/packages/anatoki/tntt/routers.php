<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api', 'namespace' => 'TNTT\Controllers'], function () {

    // Auto pull git when push new commits
    Route::post('auto-pull', 'HelperController@postAutoPull');

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

    Route::apiResources([
        'phan-quyen'     => 'PhanQuyenController',
    ], ['except' => ['store', 'destroy']]);

    Route::apiResources([
        'nhom-tai-khoan' => 'PhanNhomController',
    ]);

    Route::apiResource('khoa-hoc', 'KhoaHocController', ['except' => ['destroy']]);
    Route::apiResource('thiet-bi', 'ThietBiController', ['except' => ['show']]);
    Route::apiResource('thu-moi', 'ThuMoiController');
});