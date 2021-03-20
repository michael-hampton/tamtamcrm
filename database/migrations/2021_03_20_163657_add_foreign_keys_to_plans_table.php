<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreign('customer_id', 'plans_ibfk_1')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id', 'plans_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('domain_id', 'plans_ibfk_3')->references('id')->on('domains')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign('plans_ibfk_1');
            $table->dropForeign('plans_ibfk_2');
            $table->dropForeign('plans_ibfk_3');
        });
    }
}
