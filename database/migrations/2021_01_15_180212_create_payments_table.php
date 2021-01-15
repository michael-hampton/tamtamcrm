<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('payments_account_id_foreign');
            $table->unsignedInteger('customer_id')->index('payments_customer_id_foreign');
            $table->unsignedInteger('user_id')->nullable()->index('payments_user_id_foreign');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('company_gateway_id')->nullable()->index('payments_company_gateway_id_foreign');
            $table->unsignedInteger('type_id')->nullable()->index('payments_payment_type_id_foreign');
            $table->unsignedInteger('status_id');
            $table->decimal('amount', 16, 4)->default(0.0000);
            $table->decimal('refunded', 16, 4)->default(0.0000);
            $table->date('date')->nullable()->default('CURRENT_TIMESTAMP');
            $table->string('reference_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
            $table->tinyInteger('is_manual')->default(0);
            $table->unsignedInteger('company_id')->nullable();
            $table->string('number')->nullable();
            $table->decimal('applied', 16, 4)->default(0.0000);
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->text('private_notes')->nullable();
            $table->decimal('exchange_rate', 16, 6)->default(1.000000);
            $table->unsignedInteger('currency_id');
            $table->unsignedInteger('exchange_currency_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
