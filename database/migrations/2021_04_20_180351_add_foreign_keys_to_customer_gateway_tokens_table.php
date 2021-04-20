<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomerGatewayTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_gateway_tokens', function (Blueprint $table) {
            $table->foreign('company_gateway_id', 'customer_gateway_tokens_ibfk_1')->references('id')->on('company_gateways')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('account_id', 'customer_gateway_tokens_ibfk_2')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('customer_id', 'customer_gateway_tokens_ibfk_3')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'customer_gateway_tokens_ibfk_4')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_gateway_tokens', function (Blueprint $table) {
            $table->dropForeign('customer_gateway_tokens_ibfk_1');
            $table->dropForeign('customer_gateway_tokens_ibfk_2');
            $table->dropForeign('customer_gateway_tokens_ibfk_3');
            $table->dropForeign('customer_gateway_tokens_ibfk_4');
        });
    }
}
