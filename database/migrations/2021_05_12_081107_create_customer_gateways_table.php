<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('client_gateway_tokens_account_id_foreign');
            $table->unsignedInteger('customer_id')->nullable()->index('client_gateway_tokens_customer_id_foreign');
            $table->text('token')->nullable();
            $table->unsignedInteger('company_gateway_id')->default(1);
            $table->string('gateway_customer_reference')->nullable();
            $table->unsignedInteger('gateway_type_id')->default(1);
            $table->tinyInteger('is_default')->default(0);
            $table->text('meta')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('customer_gateways');
    }
}
