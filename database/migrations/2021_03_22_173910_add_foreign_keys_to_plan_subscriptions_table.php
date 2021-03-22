<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->foreign('plan_id', 'plan_subscriptions_ibfk_1')->references('id')->on('plans')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('domain_id', 'plan_subscriptions_ibfk_2')->references('id')->on('domains')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_subscriptions', function (Blueprint $table) {
            $table->dropForeign('plan_subscriptions_ibfk_1');
            $table->dropForeign('plan_subscriptions_ibfk_2');
        });
    }
}
