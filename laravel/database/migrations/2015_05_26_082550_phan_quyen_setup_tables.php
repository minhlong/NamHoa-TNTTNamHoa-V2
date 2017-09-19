<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PhanQuyenSetupTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create table for storing nhom_tai_khoan
        Schema::create('nhom_tai_khoan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('loai');
            $table->string('ten')->unique();
            $table->string('ten_hien_thi')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->timestamps();
        });

        // Create table for associating nhom_tai_khoan to users (Many-to-Many)
        Schema::create('taikhoan_nhomtaikhoan', function (Blueprint $table) {
            $table->char('tai_khoan_id', 5);
            $table->integer('nhom_tai_khoan_id')->unsigned();

            $table->foreign('tai_khoan_id')->references('id')->on('tai_khoan')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nhom_tai_khoan_id')->references('id')->on('nhom_tai_khoan')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['tai_khoan_id', 'nhom_tai_khoan_id']);
        });

        // Create table for storing phan_quyen
        Schema::create('phan_quyen', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ten')->unique();
            $table->string('ten_hien_thi')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->timestamps();
        });

        // Create table for associating phan_quyen to nhom_tai_khoan (Many-to-Many)
        Schema::create('phanquyen_nhomtaikhoan', function (Blueprint $table) {
            $table->integer('phan_quyen_id')->unsigned();
            $table->integer('nhom_tai_khoan_id')->unsigned();

            $table->foreign('phan_quyen_id')->references('id')->on('phan_quyen')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('nhom_tai_khoan_id')->references('id')->on('nhom_tai_khoan')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['phan_quyen_id', 'nhom_tai_khoan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('phanquyen_nhomtaikhoan');
        Schema::drop('phan_quyen');
        Schema::drop('taikhoan_nhomtaikhoan');
        Schema::drop('nhom_tai_khoan');
    }
}
