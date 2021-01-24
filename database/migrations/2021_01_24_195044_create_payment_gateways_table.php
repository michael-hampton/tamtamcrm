<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('key')->unique('gateways_key_unique');
            $table->string('provider');
            $table->unsignedInteger('default_gateway_type_id')->default(1);
            $table->timestamps();
            $table->tinyInteger('offsite_only')->default(0);
            $table->tinyInteger('is_custom')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
}
