<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('symbol');
            $table->string('precision');
            $table->string('thousands_separator');
            $table->string('decimal_mark', 20);
            $table->string('iso_code');
            $table->integer('swap_currency_symbol');
            $table->decimal('exchange_rate', 13);
            $table->timestamps();
            $table->integer('priority')->default(0);
            $table->string('subunit', 100)->nullable();
            $table->string('subunit_to_unit', 100)->nullable();
            $table->tinyInteger('symbol_first')->default(0);
            $table->string('html_entity', 10)->nullable();
            $table->integer('iso_numeric');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
