<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Shop extends Migration
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
        $this->schema->create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('internal_id');
            $table->string('platform');
            $table->string('raw_domain');
            $table->string('domain')->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('country_code')->nullable();
            $table->string('currency')->nullable();
            $table->string('iana_timezone')->nullable();
            $table->string('country')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('shop_owner', 400)->nullable();
            $table->string('money_format', 250)->nullable();
            $table->string('money_with_currency_format', 250)->nullable();
            $table->string('weight_unit', 20)->nullable();
            $table->string('plan_name',100)->nullable();
            $table->boolean('password_enabled')->nullable();
            $table->boolean('has_storefront')->nullable();
            $table->boolean('force_ssl')->nullable();
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
        $this->schema->dropIfExists('shops');
    }
}
