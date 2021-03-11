<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('invoice_id', 'tasks_ibfk_1')->references('id')->on('invoices')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'tasks_ibfk_2')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('project_id', 'tasks_ibfk_3')->references('id')->on('projects')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('source_type')->references('id')->on('source_type')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_account_id_foreign');
            $table->dropForeign('tasks_customer_id_foreign');
            $table->dropForeign('tasks_ibfk_1');
            $table->dropForeign('tasks_ibfk_2');
            $table->dropForeign('tasks_ibfk_3');
            $table->dropForeign('tasks_source_type_foreign');
        });
    }
}
