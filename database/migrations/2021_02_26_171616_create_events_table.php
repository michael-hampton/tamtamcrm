<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('beginDate');
            $table->dateTime('endDate');
            $table->unsignedInteger('customer_id')->index('customer_id');
            $table->string('location', 100);
            $table->unsignedInteger('created_by')->index('created_by');
            $table->string('title', 100);
            $table->timestamps();
            $table->unsignedInteger('event_type')->default(1)->index('event_type');
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('account_id');
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
        Schema::dropIfExists('events');
    }
}
