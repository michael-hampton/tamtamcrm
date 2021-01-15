<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->foreign('user_id', 'emails_ibfk_1')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('account_id', 'emails_ibfk_2')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign('emails_ibfk_1');
            $table->dropForeign('emails_ibfk_2');
        });
    }
}
