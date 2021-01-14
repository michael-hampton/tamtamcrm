<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPromocodeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promocode_user', function (Blueprint $table) {
            $table->foreign('order_id', 'promocode_user_ibfk_1')->references('id')->on('product_task')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('customer_id', 'promocode_user_ibfk_2')->references('id')->on('customers')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('promocode_id')->references('id')->on('promocodes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promocode_user', function (Blueprint $table) {
            $table->dropForeign('promocode_user_ibfk_1');
            $table->dropForeign('promocode_user_ibfk_2');
            $table->dropForeign('promocode_user_promocode_id_foreign');
        });
    }
}
