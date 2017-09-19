<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKhoaHocTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('khoa_hoc', function (Blueprint $table) {
            $table->increments('id');
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->tinyInteger('so_dot_kiem_tra');
            $table->tinyInteger('so_lan_kiem_tra');
            $table->unsignedSmallInteger('ngung_diem_danh');
            $table->unsignedSmallInteger('cap_nhat_dot_kiem_tra');
            $table->string('xep_hang');
            $table->string('xep_loai');
            $table->string('di_hoc');
            $table->string('di_le');
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('khoa_hoc');
    }
}
