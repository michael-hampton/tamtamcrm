<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code')->unique('code');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 4);
            $table->string('interval_unit', 100)->default('month');
            $table->unsignedSmallInteger('interval_count')->default(1);
            $table->unsignedSmallInteger('trial_period')->nullable()->default(0);
            $table->smallInteger('sort_order')->nullable();
            $table->timestamps();
            $table->tinyInteger('is_active')->default(1);
            $table->decimal('signup_fee', 10, 0)->default(0);
            $table->string('currency', 3);
            $table->unsignedSmallInteger('invoice_period')->default(0);
            $table->string('invoice_interval', 100)->default('month');
            $table->unsignedSmallInteger('grace_period')->default(0);
            $table->string('grace_interval', 100)->default('day');
            $table->unsignedTinyInteger('prorate_day')->nullable();
            $table->unsignedTinyInteger('prorate_period')->nullable();
            $table->unsignedTinyInteger('prorate_extend_due')->nullable();
            $table->unsignedSmallInteger('active_subscribers_limit')->nullable();
            $table->string('trial_interval', 100)->default('day');
            $table->softDeletes();
            $table->tinyInteger('is_locked')->default(0);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->tinyInteger('auto_billing_enabled')->default(0);
            $table->tinyInteger('can_cancel_plan')->default(1);
            $table->unsignedInteger('account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
