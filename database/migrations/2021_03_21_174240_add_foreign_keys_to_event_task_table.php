<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEventTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_task', function (Blueprint $table) {
            $table->foreign('task_id', 'event_task_ibfk_1')->references('id')->on('tasks')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('event_id', 'event_task_ibfk_2')->references('id')->on('events')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_task', function (Blueprint $table) {
            $table->dropForeign('event_task_ibfk_1');
            $table->dropForeign('event_task_ibfk_2');
        });
    }
}
