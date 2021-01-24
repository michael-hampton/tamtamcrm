<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->foreign('bank_id', 'bank_accounts_ibfk_1')->references('id')->on('banks')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('account_id', 'bank_accounts_ibfk_2')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'bank_accounts_ibfk_3')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropForeign('bank_accounts_ibfk_1');
            $table->dropForeign('bank_accounts_ibfk_2');
            $table->dropForeign('bank_accounts_ibfk_3');
        });
    }
}
