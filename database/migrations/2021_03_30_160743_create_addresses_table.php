<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('zip')->nullable();
            $table->string('state_code')->nullable();
            $table->string('city')->nullable();
            $table->integer('province_id')->nullable();
            $table->unsignedInteger('country_id')->default(225)->index();
            $table->unsignedInteger('customer_id')->index();
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('address_type')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
