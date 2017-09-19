<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThuMoiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thu_moi', function (Blueprint $table) {
            $table->increments('id');
            $table->char('tai_khoan_id', 5);
            $table->date('ngay');
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
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('thu_moi');
    }
}
