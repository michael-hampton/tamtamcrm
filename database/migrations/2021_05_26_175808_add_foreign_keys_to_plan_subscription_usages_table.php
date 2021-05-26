<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPlanSubscriptionUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_subscription_usages', function (Blueprint $table) {
            $table->foreign('subscription_id', 'plan_subscription_usages_ibfk_1')->references('id')->on('plan_subscriptions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('feature_id', 'plan_subscription_usages_ibfk_2')->references('id')->on('plan_features')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_subscription_usages', function (Blueprint $table) {
            $table->dropForeign('plan_subscription_usages_ibfk_1');
            $table->dropForeign('plan_subscription_usages_ibfk_2');
        });
    }
}
