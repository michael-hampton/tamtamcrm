<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEventUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_user', function (Blueprint $table) {
            $table->foreign('event_id', 'event_user_ibfk_1')->references('id')->on('events')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'event_user_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_user', function (Blueprint $table) {
            $table->dropForeign('event_user_ibfk_1');
            $table->dropForeign('event_user_ibfk_2');
        });
    }
}
