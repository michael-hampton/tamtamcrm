<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->nullable();
            $table->string('subdomain')->nullable();
            $table->string('portal_domain')->nullable();
            $table->smallInteger('enable_modules')->default(0);
            $table->text('custom_fields');
            $table->text('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('domain_id')->default(1)->index();
            $table->string('slack_webhook_url')->nullable();
            $table->integer('transaction_fee')->default(0);
            $table->string('support_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
