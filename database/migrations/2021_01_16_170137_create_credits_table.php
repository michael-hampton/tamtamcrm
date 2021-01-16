<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('user_id')->index('credits_user_id_foreign');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('status_id');
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->decimal('total', 16, 4);
            $table->decimal('balance', 16, 4);
            $table->dateTime('last_viewed')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->text('footer')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->text('line_items')->nullable();
            $table->date('due_date')->nullable();
            $table->dateTime('partial_due_date')->nullable();
            $table->tinyInteger('is_amount_discount')->default(0);
            $table->string('po_number')->nullable();
            $table->decimal('discount_total', 16, 4)->default(0.0000);
            $table->decimal('tax_total', 16, 4)->default(0.0000);
            $table->text('private_notes')->nullable();
            $table->string('tax_rate_name')->nullable();
            $table->decimal('tax_rate', 13, 3)->default(0.000);
            $table->unsignedInteger('design_id')->nullable();
            $table->decimal('transaction_fee', 16, 4)->nullable();
            $table->decimal('shipping_cost', 16, 4)->nullable();
            $table->tinyInteger('transaction_fee_tax')->default(0);
            $table->tinyInteger('shipping_cost_tax')->default(0);
            $table->decimal('sub_total', 16, 4);
            $table->decimal('partial', 16, 4);
            $table->decimal('gateway_fee', 16, 4)->default(0.0000);
            $table->tinyInteger('gateway_percentage')->default(0);
            $table->dateTime('date_reminder_last_sent')->nullable();
            $table->integer('currency_id')->nullable();
            $table->decimal('exchange_rate', 12)->default(0.00);
            $table->tinyInteger('gateway_fee_applied')->default(0);
            $table->integer('project_id')->nullable();
            $table->decimal('tax_2', 13, 3)->nullable()->default(0.000);
            $table->decimal('tax_3', 13, 3)->nullable()->default(0.000);
            $table->string('tax_rate_name_2', 100)->nullable();
            $table->string('tax_rate_name_3', 100)->nullable();
            $table->tinyInteger('viewed')->default(0);
            $table->unique(['account_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credits');
    }
}
