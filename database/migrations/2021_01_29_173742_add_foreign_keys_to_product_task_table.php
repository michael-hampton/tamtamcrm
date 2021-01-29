<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_task', function (Blueprint $table) {
            $table->foreign('account_id', 'product_task_ibfk_1')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('customer_id', 'product_task_ibfk_2')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('task_id')->references('id')->on('tasks')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_task', function (Blueprint $table) {
            $table->dropForeign('product_task_ibfk_1');
            $table->dropForeign('product_task_ibfk_2');
            $table->dropForeign('product_task_task_id_foreign');
        });
    }
}
