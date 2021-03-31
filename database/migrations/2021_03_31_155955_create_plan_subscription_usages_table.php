<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanSubscriptionUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_subscription_usages', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('subscription_id')->index('subscription_id');
            $table->unsignedInteger('feature_id')->index('feature_id');
            $table->unsignedSmallInteger('used');
            $table->dateTime('valid_until')->nullable();
            $table->string('timezone', 100)->nullable();
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
        Schema::dropIfExists('plan_subscription_usages');
    }
}
