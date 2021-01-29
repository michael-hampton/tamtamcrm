<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedInteger('bank_id')->index('bank_id');
            $table->unsignedInteger('parent_id');
            $table->string('username');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->string('name');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->text('private_notes')->nullable();
            $table->text('public_notes')->nullable();
            $table->unsignedInteger('user_id')->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
