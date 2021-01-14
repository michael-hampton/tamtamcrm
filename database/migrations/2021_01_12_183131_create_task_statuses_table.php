<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->string('icon');
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->integer('task_type')->default(1);
            $table->string('column_color');
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->nullable()->index('user_id');
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_statuses');
    }
}
