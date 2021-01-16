<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('client_contacts_account_id_index');
            $table->unsignedInteger('customer_id')->index('client_contacts_customer_id_index');
            $table->unsignedInteger('user_id')->index('client_contacts_user_id_index');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->string('email', 100)->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->tinyInteger('is_primary')->default(0);
            $table->tinyInteger('confirmed')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->smallInteger('failed_logins')->nullable();
            $table->string('accepted_terms_version')->nullable();
            $table->string('avatar')->nullable();
            $table->string('avatar_type')->nullable();
            $table->string('avatar_size')->nullable();
            $table->string('password');
            $table->tinyInteger('is_locked')->default(0);
            $table->tinyInteger('send_email')->default(1);
            $table->string('contact_key')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_contacts');
    }
}
