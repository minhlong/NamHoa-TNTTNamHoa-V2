<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLopHocTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lop_hoc', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('khoa_hoc_id');
            $table->string('nganh');
            $table->string('cap');
            $table->string('doi');
            $table->string('tro_giang')->nullable();
            $table->string('vi_tri_hoc')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->unique(array(
                'khoa_hoc_id',
                'nganh',
                'cap',
                'doi',
            ));

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
        Schema::drop('lop_hoc');
    }
}
