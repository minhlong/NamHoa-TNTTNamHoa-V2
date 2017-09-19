<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaikhoanLophocTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('taikhoan_lophoc', function (Blueprint $table) {
            $table->increments('id');
            $table->char('trang_thai')->nullable();
            $table->char('tai_khoan_id', 5);
            $table->unsignedInteger('lop_hoc_id');
            $table->double('chuyen_can', 5, 2)->nullable();
            $table->double('hoc_luc', 5, 2)->nullable();
            $table->string('xep_hang')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->text('nhan_xet')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->unique(array(
                'tai_khoan_id',
                'lop_hoc_id',
            ));

            $table->foreign('tai_khoan_id')
                ->references('id')->on('tai_khoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('lop_hoc_id')
                ->references('id')->on('lop_hoc')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('taikhoan_lophoc');
    }
}
