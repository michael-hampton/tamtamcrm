<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id')->index('plan_id');
            $table->string('name', 100);
            $table->tinyInteger('cancelled_immediately')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->string('subscriber_type', 100);
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedInteger('domain_id')->index('domain_id');
            $table->timestamp('due_date')->nullable();
            $table->integer('number_of_licences')->default(1);
            $table->string('promocode')->nullable();
            $table->tinyInteger('promocode_applied')->default(0);
            $table->unsignedInteger('account_id');
            $table->softDeletes();
            $table->tinyInteger('auto_renew')->default(1);
            $table->decimal('amount_owing', 12);
            $table->unique(['subscriber_type', 'subscriber_id', 'plan_id'], 'subscriber_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_subscriptions');
    }
}
