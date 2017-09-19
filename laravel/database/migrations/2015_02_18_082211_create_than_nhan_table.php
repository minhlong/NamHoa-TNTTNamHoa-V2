<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThanNhanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('than_nhan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('gia_pha_id')->nullable();
            $table->string('loai_quan_he');
            $table->string('ho_va_ten');
            $table->string('dien_thoai')->nullable();
            $table->string('ghi_chu')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->foreign('gia_pha_id')
                ->references('id')->on('gia_pha')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('than_nhan');
    }
}
