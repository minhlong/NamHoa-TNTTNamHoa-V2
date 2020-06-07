<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api', 'namespace' => 'TNTT\Controllers'], function () {

    // Auto pull git when push new commits
    Route::post('auto-pull', 'PullGitController@postAutoPull');

    /* Trang Chu */
    Route::get('trang-chu', 'TrangChuController@getThongTin');

    Route::post('dang-nhap', 'DangNhapController@dangNhap');
    Route::post('dang-xuat', 'DangNhapController@dangXuat');
    Route::post('refresh', 'DangNhapController@refresh');

    /* Tai Khoan */
    Route::group(['prefix' => 'tai-khoan'], function () {
        Route::get(null, 'TaiKhoanController@getDanhSach');
        Route::get('export', 'TaiKhoanController@generateExcelFile');
        Route::put('ngay-them-suc', 'TaiKhoanController@postThemSuc');
        Route::put('ngay-ruoc-le', 'TaiKhoanController@postRuocLe');

        Route::post('import/validate', 'TaiKhoanController@postTapTin');
        Route::post('import', 'TaiKhoanController@postTao');
        Route::get('import', 'TaiKhoanController@postTaoDownload');

        Route::get('{taiKhoan}', 'TaiKhoanController@getThongTin');
        Route::put('{taiKhoan}', 'TaiKhoanController@postUpdate');
        Route::put('{taiKhoan}/mat-khau', 'TaiKhoanController@postMatKhau');
        Route::delete('{taiKhoan}', 'TaiKhoanController@postXoa');
    });

    Route::apiResources([
        'phan-quyen'     => 'PhanQuyenController',
        'nhom-tai-khoan' => 'PhanNhomController',
    ], ['except' => ['store', 'destroy']]);

    /* Khoa Hoc */
    Route::group(['prefix' => 'khoa-hoc'], function () {
        Route::get(null, 'KhoaHocController@getDanhSach');
        // Route::post(null, 'KhoaHocController@postTaoMoi')->middleware(['permission:Hệ Thống']);

        // Route::get('{khoaHoc}', 'KhoaHocController@getThongTin');
        // Route::post(null, 'KhoaHocController@postThongTin')->middleware(['permission:Hệ Thống']);
    });

    /* Lop Hoc */
    Route::group(['prefix' => 'lop-hoc'], function () {
        Route::get('khoa-{khoaID}', 'LopHocController@getDanhSachTheoKhoa');
        Route::get('{lopHoc}', 'LopHocController@getThongTin');

        // Route::post(null, 'LopHocController@post');
        // Route::post('tap-tin', 'LopHocController@postTapTin')->middleware(['can:Lớp Học']);
        // Route::post('tap-tin/tao', 'LopHocController@postTao')->middleware(['can:Lớp Học']);
        // Route::post('{LopHoc}', 'LopHocController@postUpdate')->middleware(['can:Lớp Học']);
        // Route::post('{LopHoc}/thanh-vien', 'LopHocController@postThemThanhVien')->middleware(['can:Lớp Học']);
        // Route::post('{LopHoc}/thanh-vien/xoa', 'LopHocController@postXoaThanhVien')->middleware(['can:Lớp Học']);
        // Route::delete('{LopHoc}', 'LopHocController@postXoa')->middleware(['can:Lớp Học']);
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
});