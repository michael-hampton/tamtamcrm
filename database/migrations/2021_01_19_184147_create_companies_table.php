<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique('name');
            $table->timestamps();
            $table->string('website');
            $table->string('phone_number');
            $table->string('email');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('town');
            $table->string('city');
            $table->string('postcode');
            $table->unsignedInteger('country_id')->index('country_id');
            $table->unsignedInteger('currency_id')->nullable()->index('currency_id');
            $table->integer('industry_id')->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $table->text('private_notes')->nullable();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('account_id')->index();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->softDeletes();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('vat_number')->nullable();
            $table->decimal('balance', 16, 4)->nullable();
            $table->decimal('paid_to_date', 16, 4)->nullable();
            $table->string('number')->nullable();
            $table->string('logo')->nullable();
            $table->text('public_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
