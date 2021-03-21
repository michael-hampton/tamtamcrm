<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPlanFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->foreign('feature_id', 'plan_features_ibfk_1')->references('id')->on('features')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('plan_id', 'plan_features_ibfk_2')->references('id')->on('plans')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropForeign('plan_features_ibfk_1');
            $table->dropForeign('plan_features_ibfk_2');
        });
    }
}
