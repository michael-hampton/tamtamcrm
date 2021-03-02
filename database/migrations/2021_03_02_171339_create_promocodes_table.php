<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 32)->unique();
            $table->double('reward', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->unsignedInteger('account_id')->index('account_id');
            $table->text('data')->nullable();
            $table->tinyInteger('is_disposable')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->enum('amount_type', ['amt', 'pct', '', ''])->default('amt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocodes');
    }
}
