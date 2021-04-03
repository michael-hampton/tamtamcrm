<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommentTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_task', function (Blueprint $table) {
            $table->foreign('comment_id', 'task_comment_comment_id_foreign')->references('id')->on('comments')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_task', function (Blueprint $table) {
            $table->dropForeign('task_comment_comment_id_foreign');
        });
    }
}
