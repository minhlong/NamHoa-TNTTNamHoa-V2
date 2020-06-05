<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppInstall extends Migration
{
    protected $schema;

    public function __construct()
    {
        $this->schema = Schema::connection(config('fireapps.core_db_connections'));
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('app_install', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->integer('shop_id');
            $table->string('access_token')->nullable();
            $table->boolean('is_charge')->default(false);
            $table->string('charge_id')->nullable();
            $table->string('app_plan')->nullable();
            $table->boolean('status')->default(0);
            $table->string('on_boarding')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('app_install');
    }
}
