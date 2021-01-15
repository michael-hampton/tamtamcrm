<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id')->nullable()->index('product_task_task_id_foreign');
            $table->timestamps();
            $table->integer('user_id');
            $table->unsignedInteger('account_id')->index('account_id');
            $table->unsignedInteger('status_id')->default(1)->index('status');
            $table->decimal('balance', 16, 4);
            $table->decimal('tax_total', 16, 4);
            $table->decimal('sub_total', 16, 4);
            $table->decimal('discount_total', 16, 4);
            $table->decimal('total', 16, 4);
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->text('line_items')->nullable();
            $table->string('tax_rate_name')->nullable();
            $table->decimal('tax_rate', 13, 3)->nullable();
            $table->date('date');
            $table->decimal('partial', 16, 4)->nullable();
            $table->dateTime('partial_due_date')->nullable();
            $table->unsignedInteger('customer_id')->index('customer_id');
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->tinyInteger('is_amount_discount')->default(0);
            $table->unsignedInteger('design_id')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('transaction_fee', 16, 4)->nullable();
            $table->decimal('shipping_cost', 16, 4)->nullable();
            $table->tinyInteger('transaction_fee_tax')->default(0);
            $table->tinyInteger('shipping_cost_tax')->default(0);
            $table->string('number')->nullable();
            $table->softDeletes();
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('po_number', 100)->nullable();
            $table->integer('previous_status')->nullable();
            $table->string('shipping_id', 100)->nullable();
            $table->string('shipping_label_url')->nullable();
            $table->string('voucher_code')->nullable();
            $table->decimal('gateway_fee', 16, 4)->default(0.0000);
            $table->dateTime('date_cancelled')->nullable();
            $table->tinyInteger('gateway_percentage')->default(0);
            $table->unsignedInteger('assigned_to')->nullable();
            $table->dateTime('date_reminder_last_sent')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('exchange_rate', 12)->default(0.00);
            $table->tinyInteger('gateway_fee_applied')->default(0);
            $table->unsignedInteger('project_id')->nullable();
            $table->decimal('tax_2', 13, 3)->nullable()->default(0.000);
            $table->decimal('tax_3', 13, 3)->nullable()->default(0.000);
            $table->string('tax_rate_name_2', 100)->nullable();
            $table->string('tax_rate_name_3', 100)->nullable();
            $table->unsignedInteger('payment_id')->nullable();
            $table->tinyInteger('payment_taken')->default(0);
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
        Schema::dropIfExists('product_task');
    }
}
