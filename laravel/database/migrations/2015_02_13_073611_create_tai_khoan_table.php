<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaiKhoanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tai_khoan', function (Blueprint $table) {
            $table->char('id', 5);
            $table->string('mat_khau', 60);
            $table->string('loai_tai_khoan');
            $table->string('trang_thai');
            $table->string('gioi_tinh')->default('NAM');
            $table->string('ten_thanh')->nullable();
            $table->string('ho_va_ten');
            $table->string('ten');
            $table->date('ngay_sinh')->nullable();
            $table->date('ngay_rua_toi')->nullable();
            $table->date('ngay_ruoc_le')->nullable();
            $table->date('ngay_them_suc')->nullable();
            $table->string('email')->nullable();
            $table->string('dien_thoai')->nullable();
            $table->string('dia_chi')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('giao_ho')->nullable();
            // $table->unsignedInteger('gia_pha_id')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            // $table->foreign('gia_pha_id')
            //     ->references('id')->on('gia_pha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('tai_khoan');
    }
}
