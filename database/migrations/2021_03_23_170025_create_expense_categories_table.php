<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('account_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('column_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_categories');
    }
}
