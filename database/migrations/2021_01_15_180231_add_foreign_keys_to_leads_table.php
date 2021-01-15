<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('account_id', 'leads_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'leads_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('source_type', 'leads_ibfk_3')->references('id')->on('source_type')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('industry_id', 'leads_ibfk_5')->references('id')->on('industries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('assigned_to', 'leads_ibfk_6')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign('leads_ibfk_1');
            $table->dropForeign('leads_ibfk_2');
            $table->dropForeign('leads_ibfk_3');
            $table->dropForeign('leads_ibfk_5');
            $table->dropForeign('leads_ibfk_6');
        });
    }
}
