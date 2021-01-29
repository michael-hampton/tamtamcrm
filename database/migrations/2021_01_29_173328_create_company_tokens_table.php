<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->unsignedInteger('domain_id')->index('company_tokens_domain_id_foreign');
            $table->unsignedInteger('user_id')->index('company_tokens_user_id_foreign');
            $table->text('token')->nullable();
            $table->string('name')->nullable();
            $table->smallInteger('is_web')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_tokens');
    }
}
