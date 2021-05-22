<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToRecurringInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::table('recurring_invoices', function (Blueprint $table) {
            $table->dropForeign('recurring_invoices_account_id_foreign');
            $table->dropForeign('recurring_invoices_customer_id_foreign');
            $table->dropForeign('recurring_invoices_user_id_foreign');
        });
    }
}
