<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_photo')->nullable();
            $table->string('username');
            $table->timestamps();
            $table->string('email')->default('1');
            $table->string('password');
            $table->text('auth_token')->nullable()->unique('users_api_token_unique');
            $table->integer('is_active')->default(1);
            $table->softDeletes();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->date('dob')->nullable();
            $table->string('job_description')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->tinyInteger('hide')->default(0);
            $table->string('accepted_terms_version', 100)->nullable();
            $table->string('confirmation_code', 100)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->string('ip', 200)->nullable();
            $table->unsignedInteger('domain_id')->index('domain_id');
            $table->string('two_factor_token');
            $table->dateTime('two_factor_expiry')->nullable();
            $table->string('google_id')->nullable();
            $table->string('previous_email_address')->nullable();
            $table->tinyInteger('two_factor_authentication_enabled')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->dateTime('password_verified_at')->nullable();
            $table->string('google_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
