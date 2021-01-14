<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('customers_account_id_foreign');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('currency_id')->nullable();
            $table->unsignedInteger('company_id')->nullable()->index('company_id');
            $table->unsignedInteger('default_payment_method')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('status')->default(1);
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('balance', 16, 4)->default(0.0000);
            $table->decimal('paid_to_date', 16, 4)->default(0.0000);
            $table->decimal('credit_balance', 16, 4)->default(0.0000);
            $table->dateTime('last_login')->nullable();
            $table->text('settings')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->unsignedInteger('group_id')->nullable();
            $table->string('vat_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->string('number')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->unsignedInteger('industry_id')->nullable()->index('industry_id');
            $table->unsignedInteger('size_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
