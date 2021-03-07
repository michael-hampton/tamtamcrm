<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('product_id', 'reviews_ibfk_1')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('client_contact_id', 'reviews_ibfk_2')->references('id')->on('customer_contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('account_id', 'reviews_ibfk_3')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign('reviews_ibfk_1');
            $table->dropForeign('reviews_ibfk_2');
            $table->dropForeign('reviews_ibfk_3');
        });
    }
}
