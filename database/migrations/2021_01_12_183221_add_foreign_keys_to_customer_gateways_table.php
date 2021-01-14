<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomerGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_gateways', function (Blueprint $table) {
            $table->foreign('account_id', 'client_gateway_tokens_account_id_foreign')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('customer_id', 'client_gateway_tokens_customer_id_foreign')->references('id')->on('customers')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_gateways', function (Blueprint $table) {
            $table->dropForeign('client_gateway_tokens_account_id_foreign');
            $table->dropForeign('client_gateway_tokens_customer_id_foreign');
        });
    }
}
