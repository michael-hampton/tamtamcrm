<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->string('column_color');
            $table->dateTime('due_date');
            $table->tinyInteger('is_completed')->default(0);
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->integer('task_status_id');
            $table->integer('created_by');
            $table->unsignedInteger('rating')->nullable();
            $table->unsignedInteger('customer_id')->index('tasks_customer_id_foreign');
            $table->decimal('valued_at')->nullable();
            $table->integer('parent_id')->default(0);
            $table->unsignedInteger('source_type')->nullable()->default(1)->index('tasks_source_type_foreign');
            $table->dateTime('start_date')->nullable();
            $table->softDeletes();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('account_id')->index();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->integer('company_id')->nullable();
            $table->smallInteger('task_status_sort_order')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->text('custom_value3')->nullable();
            $table->text('custom_value4')->nullable();
            $table->unsignedInteger('project_id')->nullable()->index('project_id');
            $table->unsignedInteger('invoice_id')->nullable()->index('invoice_id');
            $table->unsignedInteger('user_id')->default(9874)->index('user_id');
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->tinyInteger('is_recurring')->default(0);
            $table->dateTime('recurring_start_date')->nullable();
            $table->dateTime('recurring_end_date')->nullable();
            $table->dateTime('last_sent_date')->nullable();
            $table->dateTime('next_send_date')->nullable();
            $table->enum('recurring_frequency', ['DAILY', 'MONTHLY', 'WEEKLY', 'FORTNIGHT', 'TWO_MONTHS', 'THREE_MONTHS', 'FOUR_MONTHS', 'SIX_MONTHS', 'YEARLY'])->nullable()->default('MONTHLY');
            $table->dateTime('recurring_due_date')->nullable();
            $table->integer('number')->nullable();
            $table->unsignedInteger('design_id')->nullable();
            $table->tinyInteger('include_documents')->default(1);
            $table->double('task_rate')->nullable();
            $table->integer('task_sort_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
