<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
            $table->integer('number_of_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_terms');
    }
}
