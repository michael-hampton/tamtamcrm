<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerGatewayTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_gateway_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('customer_id')->index('customer_id');
            $table->string('token');
            $table->text('data');
            $table->unsignedInteger('gateway_type_id');
            $table->unsignedInteger('company_gateway_id')->index('company_gateway_id');
            $table->string('customer_reference', 100);
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_gateway_tokens');
    }
}
