<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->foreign('account_id', 'cases_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'cases_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('customer_id', 'cases_ibfk_3')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('category_id', 'cases_ibfk_4')->references('id')->on('case_categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropForeign('cases_ibfk_1');
            $table->dropForeign('cases_ibfk_2');
            $table->dropForeign('cases_ibfk_3');
            $table->dropForeign('cases_ibfk_4');
        });
    }
}
