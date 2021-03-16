<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable()->index('products_brand_id_index');
            $table->string('sku');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->string('cover')->nullable();
            $table->decimal('quantity', 16, 4)->default(0.0000);
            $table->decimal('cost', 16, 4)->default(0.0000);
            $table->softDeletes();
            $table->unsignedInteger('account_id')->index('products_account_id_foreign');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('reserved_stock')->nullable()->default(0);
            $table->integer('rating')->nullable()->default(0);
            $table->integer('ratings_count')->default(0);
            $table->decimal('length', 5)->nullable()->default(0.00);
            $table->decimal('width', 5)->nullable()->default(0.00);
            $table->decimal('height', 5)->nullable()->default(0.00);
            $table->decimal('weight', 5)->nullable()->default(0.00);
            $table->enum('mass_unit', ['oz', 'gms', 'lbs', ''])->nullable();
            $table->enum('distance_unit', ['cm', 'mtr', 'in', 'mm', 'ft', 'yd'])->nullable();
            $table->unsignedInteger('brand_id')->nullable()->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
