<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocode_user', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->index('customer_id');
            $table->unsignedInteger('promocode_id')->index('promocode_user_promocode_id_foreign');
            $table->timestamp('used_at')->useCurrent();
            $table->unsignedInteger('order_id')->index('order_id');
            $table->integer('id', true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocode_user');
    }
}
