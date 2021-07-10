<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
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
            $table->unsignedInteger('source_type')->nullable()->default(1)->index('tasks_source_type_foreign');
            $table->softDeletes();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('account_id')->index('tasks_account_id_index');
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->tinyInteger('hide')->default(0);
            $table->text('custom_value3')->nullable();
            $table->text('custom_value4')->nullable();
            $table->unsignedInteger('invoice_id')->nullable()->index('invoice_id');
            $table->unsignedInteger('user_id')->default(9874)->index('user_id');
            $table->text('internal_note')->nullable();
            $table->text('customer_note')->nullable();
            $table->integer('number')->nullable();
            $table->unsignedInteger('design_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('project_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
