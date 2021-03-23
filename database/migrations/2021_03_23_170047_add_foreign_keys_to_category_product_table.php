<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCategoryProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id', 'category_product_ibfk_1')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('product_id', 'category_product_ibfk_2')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign('category_product_ibfk_1');
            $table->dropForeign('category_product_ibfk_2');
        });
    }
}
