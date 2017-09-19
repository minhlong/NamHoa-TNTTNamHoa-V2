<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiemSoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('diem_so', function (Blueprint $table) {
            $table->increments('id');
            $table->char('tai_khoan_id', 5);
            $table->unsignedInteger('khoa_hoc_id');
            $table->char('phan_loai')->nullable();
            $table->tinyInteger('dot');
            $table->tinyInteger('lan');
            $table->double('diem', 5, 2)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->foreign('tai_khoan_id')
                ->references('id')->on('tai_khoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('khoa_hoc_id')
                ->references('id')->on('khoa_hoc')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('diem_so');
    }
}
