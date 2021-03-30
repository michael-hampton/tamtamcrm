<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->foreign('plan_id', 'domains_ibfk_1')->references('id')->on('plans')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('customer_id', 'domains_ibfk_2')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropForeign('domains_ibfk_1');
            $table->dropForeign('domains_ibfk_2');
        });
    }
}
