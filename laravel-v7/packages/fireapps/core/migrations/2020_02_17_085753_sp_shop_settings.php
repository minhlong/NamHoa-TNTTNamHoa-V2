<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpShopSettings extends Migration
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
        $this->schema->create('sp_shop_settings', function (Blueprint $table) {
            $table->integer('shop_id');
            $table->boolean('is_shorten_link')->default(false);
            $table->string('service_shorten_link');
            $table->string('shorten_link_bit_ly_token')->nullable();
            $table->string('shorten_link_bit_ly_info')->nullable();
            $table->string('timezone');
            $table->string('time_format');
            $table->string('date_format');
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
        $this->schema->dropIfExists('sp_shop_settings');
    }
}
