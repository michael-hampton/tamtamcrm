<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('user_id')->index('invoices_user_id_foreign');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('status_id');
            $table->unsignedInteger('recurring_invoice_id')->nullable();
            $table->string('number')->nullable();
            $table->tinyInteger('is_amount_discount')->default(0);
            $table->integer('is_recurring')->nullable();
            $table->string('po_number')->nullable();
            $table->date('date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('recurring_due_date')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->text('line_items')->nullable();
            $table->text('footer')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('terms')->nullable();
            $table->decimal('total', 16, 4);
            $table->decimal('sub_total', 16, 4);
            $table->decimal('tax_total', 16, 4);
            $table->decimal('discount_total', 16, 4);
            $table->integer('parent_id')->nullable();
            $table->integer('frequency')->nullable();
            $table->decimal('balance', 16, 4);
            $table->decimal('partial', 16, 4)->nullable();
            $table->dateTime('partial_due_date')->nullable();
            $table->dateTime('last_viewed')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('task_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->text('private_notes')->nullable();
            $table->tinyInteger('uses_inclusive_taxes')->default(0);
            $table->decimal('tax_rate', 13, 3)->nullable()->default(0.000);
            $table->string('tax_rate_name')->nullable();
            $table->unsignedInteger('design_id')->nullable();
            $table->decimal('transaction_fee', 16, 4)->nullable();
            $table->decimal('shipping_cost', 16, 4)->nullable();
            $table->tinyInteger('transaction_fee_tax')->default(0);
            $table->tinyInteger('shipping_cost_tax')->default(0);
            $table->unsignedInteger('order_id');
            $table->integer('previous_status')->nullable();
            $table->decimal('previous_balance', 16, 4)->nullable()->default(0.0000);
            $table->decimal('gateway_fee', 16, 4)->nullable()->default(0.0000);
            $table->string('voucher_code')->nullable();
            $table->tinyInteger('commission_paid')->default(0);
            $table->dateTime('commission_paid_date')->nullable();
            $table->dateTime('date_cancelled')->nullable();
            $table->tinyInteger('gateway_percentage')->default(0);
            $table->dateTime('date_reminder_last_sent')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('exchange_rate', 12)->default(0.00);
            $table->tinyInteger('gateway_fee_applied')->default(0);
            $table->decimal('late_fee_charge', 12)->default(0.00);
            $table->dateTime('date_to_send')->nullable();
            $table->text('temp_data')->nullable();
            $table->tinyInteger('auto_billing_enabled')->default(0);
            $table->integer('project_id')->nullable();
            $table->decimal('tax_2', 13, 3)->nullable()->default(0.000);
            $table->decimal('tax_3', 13, 3)->nullable()->default(0.000);
            $table->string('tax_rate_name_2', 100)->nullable();
            $table->string('tax_rate_name_3', 100)->nullable();
            $table->tinyInteger('viewed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
