<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->text('message');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('customer_id')->index('customer_id');
            $table->integer('status_id');
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('number')->nullable();
            $table->text('private_notes')->nullable();
            $table->unsignedInteger('category_id')->nullable()->index('category_id');
            $table->integer('priority_id')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('assigned_to');
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->unsignedInteger('contact_id')->nullable();
            $table->dateTime('date_opened')->nullable();
            $table->unsignedInteger('opened_by')->nullable();
            $table->dateTime('date_closed')->nullable();
            $table->unsignedInteger('closed_by')->nullable();
            $table->unsignedInteger('merged_case_id')->nullable();
            $table->tinyInteger('has_merged_case')->default(0);
            $table->unsignedInteger('link_project_value');
            $table->unsignedInteger('link_product_value')->nullable();
            $table->tinyInteger('overdue_email_sent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cases');
    }
}
