<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCompanyGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_gateways', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('gateway_key', 'company_gateways_ibfk_1')->references('key')->on('payment_gateways')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_gateways', function (Blueprint $table) {
            $table->dropForeign('company_gateways_account_id_foreign');
            $table->dropForeign('company_gateways_ibfk_1');
            $table->dropForeign('company_gateways_user_id_foreign');
        });
    }
}
