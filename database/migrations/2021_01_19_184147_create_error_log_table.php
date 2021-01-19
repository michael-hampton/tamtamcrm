<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_log', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('data')->nullable();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('account_id');
            $table->string('entity', 100);
            $table->integer('entity_id');
            $table->string('error_type', 100);
            $table->string('error_result', 100);
            $table->timestamps();
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
        Schema::dropIfExists('error_log');
    }
}
