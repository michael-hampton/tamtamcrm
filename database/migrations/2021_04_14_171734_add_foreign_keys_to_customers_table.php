<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('company_id', 'customers_ibfk_1')->references('id')->on('companies')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('industry_id', 'customers_ibfk_2')->references('id')->on('industries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_account_id_foreign');
            $table->dropForeign('customers_ibfk_1');
            $table->dropForeign('customers_ibfk_2');
        });
    }
}
