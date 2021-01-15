<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->timestamps();
            $table->string('column_color', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('case_categories');
    }
}
