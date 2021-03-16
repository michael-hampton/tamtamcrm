<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('job_title')->nullable();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('source_type')->nullable()->index('source_type');
            $table->unsignedInteger('task_status_id')->default(5);
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('website')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->string('name');
            $table->string('description');
            $table->string('phone', 100);
            $table->string('email');
            $table->decimal('valued_at')->nullable()->default(0.00);
            $table->string('company_name', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
            $table->unsignedInteger('assigned_to')->nullable()->index('assigned_user_id');
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->integer('country_id')->default(225);
            $table->unsignedInteger('industry_id')->nullable()->default(1)->index('industry_id');
            $table->string('number')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedInteger('design_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('task_sort_order')->nullable();
            $table->string('column_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
