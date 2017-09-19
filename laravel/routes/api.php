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
    /* Tai Khoan */
    Route::get('tai-khoan', function () {
    	return response()->json([
    		'data' => \App\TaiKhoan::all(),
    	]);
    });
    Route::get('tai-khoan/{TaiKhoan}', function ($obj) {
    	return response()->json([
    		'data' => $obj,
    	]);
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
    });
    Route::get('khoa-hoc/{KhoaHoc}', function ($obj) {
        return response()->json([
            'data' => $obj,
        ]);
    });
});

Route::bind('TaiKhoan', function ($value) {
    return \App\TaiKhoan::findOrFail($value);
});
Route::bind('LopHoc', function ($value) {
    return \App\LopHoc::findOrFail($value);
});
Route::bind('KhoaHoc', function ($value) {
    return \App\KhoaHoc::findOrFail($value);
});