<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('user_id')->index('expenses_user_id_foreign');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->unsignedInteger('invoice_currency_id');
            $table->unsignedInteger('currency_id');
            $table->unsignedInteger('expense_category_id')->nullable()->index('category_id');
            $table->unsignedInteger('payment_type_id')->nullable();
            $table->unsignedInteger('recurring_expense_id')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->decimal('amount', 13);
            $table->decimal('converted_amount', 13);
            $table->decimal('exchange_rate', 13, 4);
            $table->string('tax_rate_name')->nullable();
            $table->decimal('tax_rate', 13, 3)->default(0.000);
            $table->date('date')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('reference_number')->nullable();
            $table->tinyInteger('create_invoice')->default(0);
            $table->tinyInteger('include_documents')->nullable()->default(1);
            $table->string('transaction_id')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->integer('status_id')->default(1);
            $table->text('private_notes')->nullable();
            $table->tinyInteger('is_recurring')->default(0);
            $table->dateTime('recurring_start_date')->nullable();
            $table->dateTime('recurring_end_date')->nullable();
            $table->dateTime('last_sent_date')->nullable();
            $table->dateTime('next_send_date')->nullable();
            $table->enum('recurring_frequency', ['DAILY', 'MONTHLY', 'WEEKLY', 'FORTNIGHT', 'TWO_MONTHS', 'THREE_MONTHS', 'FOUR_MONTHS', 'SIX_MONTHS', 'YEARLY'])->nullable()->default('MONTHLY');
            $table->string('number')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->dateTime('recurring_due_date')->nullable();
            $table->decimal('tax_2', 13, 3)->nullable()->default(0.000);
            $table->decimal('tax_3', 13, 3)->nullable()->default(0.000);
            $table->string('tax_rate_name_2', 100)->nullable();
            $table->string('tax_rate_name_3', 100)->nullable();
            $table->integer('project_id')->nullable()->default(0);
            $table->dateTime('date_approved')->nullable();
            $table->decimal('amount_before_tax', 13)->default(0.00);
            $table->tinyInteger('is_amount_discount')->default(0);
            $table->tinyInteger('tax_is_amount')->default(0);
            $table->tinyInteger('amount_includes_tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
