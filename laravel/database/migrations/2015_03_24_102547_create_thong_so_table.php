<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThongSoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('thong_so', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('loai_du_lieu', ['NGANH', 'CAP', 'DOI', 'DI_HOC', 'DI_LE']);
            $table->enum('trang_thai', ['HIEN_THI', 'KHONG_HIEN_THI']);
            $table->char('ky_hieu', 20);
            $table->string('ten');
            $table->smallInteger('thu_tu');
            $table->char('tai_khoan_cap_nhat', 5)->nullable();
            $table->timestamps();

            $table->unique(array(
                'loai_du_lieu',
                'ky_hieu',
            ));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('thong_so');
    }
}
