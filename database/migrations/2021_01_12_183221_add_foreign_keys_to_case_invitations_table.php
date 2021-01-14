<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCaseInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_invitations', function (Blueprint $table) {
            $table->foreign('contact_id', 'case_invitations_ibfk_1')->references('id')->on('customer_contacts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('case_id', 'case_invitations_ibfk_2')->references('id')->on('cases')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_invitations', function (Blueprint $table) {
            $table->dropForeign('case_invitations_ibfk_1');
            $table->dropForeign('case_invitations_ibfk_2');
        });
    }
}
