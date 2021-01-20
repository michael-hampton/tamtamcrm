<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTaskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_statuses', function (Blueprint $table) {
            $table->foreign('account_id', 'task_statuses_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id', 'task_statuses_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task_statuses', function (Blueprint $table) {
            $table->dropForeign('task_statuses_ibfk_1');
            $table->dropForeign('task_statuses_ibfk_2');
        });
    }
}
