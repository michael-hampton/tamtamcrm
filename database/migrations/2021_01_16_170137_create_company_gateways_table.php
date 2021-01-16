<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('company_gateways_account_id_foreign');
            $table->unsignedInteger('user_id')->index('company_gateways_user_id_foreign');
            $table->string('gateway_key')->index('company_gateways_gateway_key_foreign');
            $table->string('accepted_credit_cards', 100);
            $table->tinyInteger('require_cvv')->default(1);
            $table->tinyInteger('update_details')->nullable()->default(0);
            $table->text('config');
            $table->text('fees_and_limits');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('exclude_from_checkout')->default(0);
            $table->tinyInteger('token_billing_enabled')->default(0);
            $table->string('name');
            $table->tinyInteger('should_store_card')->default(0);
            $table->text('fields')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_gateways');
    }
}
