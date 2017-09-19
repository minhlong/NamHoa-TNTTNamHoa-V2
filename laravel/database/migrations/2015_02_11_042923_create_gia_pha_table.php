<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiaPhaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gia_pha', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('gia_pha');
    }
}
