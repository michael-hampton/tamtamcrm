<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->text('permissions')->nullable();
            $table->text('settings')->nullable();
            $table->tinyInteger('is_owner')->default(0);
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('is_locked')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('slack_webhook_url', 200)->nullable();
            $table->text('notifications')->nullable();
            $table->enum('default_notification_type', ['mail', 'slack', '', ''])->default('mail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_user');
    }
}
