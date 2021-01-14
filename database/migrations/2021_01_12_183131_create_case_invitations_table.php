<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index('quote_invitations_account_id_foreign');
            $table->unsignedInteger('user_id')->index('quote_invitations_user_id_foreign');
            $table->unsignedInteger('contact_id')->index('quote_invitations_customer_id_foreign');
            $table->unsignedInteger('case_id')->index('quote_invitations_quote_id_index');
            $table->string('key')->index('quote_invitations_key_index');
            $table->string('transaction_reference')->nullable();
            $table->string('message_id')->nullable();
            $table->text('email_error')->nullable();
            $table->text('signature_base64')->nullable();
            $table->dateTime('signature_date')->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->dateTime('viewed_date')->nullable();
            $table->dateTime('opened_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->text('client_signature')->nullable();
            $table->index(['deleted_at', 'case_id'], 'quote_invitations_deleted_at_quote_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('case_invitations');
    }
}
