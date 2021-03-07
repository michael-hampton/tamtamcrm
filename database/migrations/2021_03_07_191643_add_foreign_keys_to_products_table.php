<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('company_id', 'products_brand_id_foreign')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('brand_id', 'products_ibfk_1')->references('id')->on('brands')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_account_id_foreign');
            $table->dropForeign('products_brand_id_foreign');
            $table->dropForeign('products_ibfk_1');
        });
    }
}
