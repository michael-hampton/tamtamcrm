<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('product_id')->index('product_id');
            $table->integer('rating');
            $table->text('comment');
            $table->timestamps();
            $table->tinyInteger('spam');
            $table->tinyInteger('approved');
            $table->unsignedInteger('client_contact_id')->index('client_contact_id');
            $table->unsignedInteger('account_id')->index('account_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
