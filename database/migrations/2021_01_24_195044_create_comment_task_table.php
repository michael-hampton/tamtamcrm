<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->index('task_comment_task_id_foreign');
            $table->unsignedInteger('comment_id')->index('task_comment_comment_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_task');
    }
}
