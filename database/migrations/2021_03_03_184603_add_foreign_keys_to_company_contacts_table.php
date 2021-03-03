<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCompanyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_contacts', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_contacts', function (Blueprint $table) {
            $table->dropForeign('company_contacts_account_id_foreign');
            $table->dropForeign('company_contacts_company_id_foreign');
            $table->dropForeign('company_contacts_user_id_foreign');
        });
    }
}
