<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->string('target_url', 100);
            $table->unsignedInteger('event_id');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->string('format')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
