<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiemDanhTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('diem_danh', function (Blueprint $table) {
            $table->increments('id');
            $table->char('tai_khoan_id', 5);
            $table->char('phan_loai')->nullable();
            $table->date('ngay');
            $table->char('di_le')->nullable();
            $table->char('di_hoc')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->foreign('tai_khoan_id')
                ->references('id')->on('tai_khoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('diem_danh');
    }
}
