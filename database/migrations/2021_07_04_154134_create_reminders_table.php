<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('enabled')->default(0);
            $table->integer('number_of_days_after');
            $table->enum('scheduled_to_send', ['after_invoice_date', 'before_due_date', 'after_due_date', '']);
            $table->decimal('amount_to_charge', 12);
            $table->enum('amount_type', ['percent', 'fixed', '', ''])->default('fixed');
            $table->timestamps();
            $table->string('subject');
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
}
