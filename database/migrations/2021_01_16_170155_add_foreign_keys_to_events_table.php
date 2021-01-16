<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreign('account_id', 'events_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('created_by', 'events_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('customer_id', 'events_ibfk_3')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('event_type', 'events_ibfk_4')->references('id')->on('event_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign('events_ibfk_1');
            $table->dropForeign('events_ibfk_2');
            $table->dropForeign('events_ibfk_3');
            $table->dropForeign('events_ibfk_4');
        });
    }
}
