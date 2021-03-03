<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCustomerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->foreign('customer_id', 'client_contacts_customer_id_foreign')->references('id')->on('customers')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_contacts', function (Blueprint $table) {
            $table->dropForeign('client_contacts_customer_id_foreign');
        });
    }
}
