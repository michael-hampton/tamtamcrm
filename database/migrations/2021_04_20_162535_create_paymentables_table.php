<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymentables', function (Blueprint $table) {
            $table->unsignedInteger('payment_id')->index('payment_id');
            $table->unsignedInteger('paymentable_id');
            $table->decimal('amount', 16, 4)->default(0.0000);
            $table->string('paymentable_type');
            $table->increments('id');
            $table->decimal('refunded', 16, 4)->default(0.0000);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymentables');
    }
}
