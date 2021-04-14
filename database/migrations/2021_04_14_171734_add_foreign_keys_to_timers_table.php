<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timers', function (Blueprint $table) {
            $table->foreign('task_id', 'timers_ibfk_1')->references('id')->on('tasks')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('account_id', 'timers_ibfk_2')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timers', function (Blueprint $table) {
            $table->dropForeign('timers_ibfk_1');
            $table->dropForeign('timers_ibfk_2');
            $table->dropForeign('timers_user_id_foreign');
        });
    }
}
