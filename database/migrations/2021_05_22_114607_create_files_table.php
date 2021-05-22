<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('files_user_id_foreign');
            $table->timestamps();
            $table->integer('is_active')->default(1);
            $table->string('file_path');
            $table->unsignedInteger('account_id')->index();
            $table->string('preview')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->unsignedInteger('fileable_id');
            $table->string('fileable_type');
            $table->unsignedInteger('company_id')->nullable();
            $table->softDeletes();
            $table->integer('assigned_to')->nullable();
            $table->tinyInteger('uploaded_by_customer')->default(0);
            $table->tinyInteger('customer_can_view')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
