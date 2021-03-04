<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('company_ledgers_account_id_foreign');
            $table->unsignedInteger('customer_id')->nullable()->index('company_ledgers_customer_id_foreign');
            $table->unsignedInteger('user_id')->nullable();
            $table->decimal('amount', 16, 4)->nullable();
            $table->decimal('updated_balance', 16, 4)->nullable();
            $table->text('notes')->nullable();
            $table->text('hash')->nullable();
            $table->unsignedInteger('transactionable_id');
            $table->string('transactionable_type');
            $table->timestamps();
            $table->decimal('original_customer_balance', 16, 4)->default(0.0000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
