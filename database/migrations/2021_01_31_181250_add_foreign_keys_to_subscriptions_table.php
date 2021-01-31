<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('account_id', 'subscriptions_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'subscriptions_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_ibfk_1');
            $table->dropForeign('subscriptions_ibfk_2');
        });
    }
}
