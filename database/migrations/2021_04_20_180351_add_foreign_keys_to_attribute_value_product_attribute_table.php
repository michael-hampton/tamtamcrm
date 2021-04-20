<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAttributeValueProductAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attribute_value_product_attribute', function (Blueprint $table) {
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_value_product_attribute', function (Blueprint $table) {
            $table->dropForeign('attribute_value_product_attribute_attribute_value_id_foreign');
            $table->dropForeign('attribute_value_product_attribute_product_attribute_id_foreign');
        });
    }
}
