<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPaymentTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->foreign('account_id', 'payment_terms_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'payment_terms_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_terms', function (Blueprint $table) {
            $table->dropForeign('payment_terms_ibfk_1');
            $table->dropForeign('payment_terms_ibfk_2');
        });
    }
}
