<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_id')->nullable()->index();
            $table->unsignedInteger('default_account_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->integer('subscription_period')->default(0);
            $table->integer('subscription_plan')->nullable();
            $table->date('subscription_expiry_date')->nullable();
            $table->integer('allowed_number_of_users')->default(99999);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
