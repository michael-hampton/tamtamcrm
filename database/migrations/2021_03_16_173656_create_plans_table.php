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
            $table->integer('id', true);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('domain_id');
            $table->date('expiry_date');
            $table->enum('plan', ['STANDARD', 'ADVANCED', 'TRIAL', '']);
            $table->enum('plan_period', ['YEARLY', 'MONTHLY', '', '']);
            $table->integer('number_of_licences');
            $table->string('promocode');
            $table->tinyInteger('promocode_applied')->default(0);
            $table->date('due_date');
            $table->date('plan_started');
            $table->date('plan_ended');
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('licence_number', 100)->nullable();
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
