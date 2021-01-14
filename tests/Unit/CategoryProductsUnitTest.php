<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryProductsUnitTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_retrieve_the_products_from_the_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->associateProduct($product);
        $products = $categoryRepo->findProducts();
        foreach ($products as $producta) {
            $this->assertEquals($product->sku, $producta->sku);
            $this->assertEquals($product->name, $producta->name);
            $this->assertEquals($product->description, $producta->description);
            $this->assertEquals($product->quantity, $producta->quantity);
            $this->assertEquals($product->price, $producta->price);
        }
    }

    /** @test */
    public function it_can_associate_the_product_in_the_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $producta = $categoryRepo->associateProduct($product);
        $this->assertEquals($product->sku, $producta->sku);
        $this->assertEquals($product->name, $producta->name);
        $this->assertEquals($product->description, $producta->description);
        $this->assertEquals($product->quantity, $producta->quantity);
        $this->assertEquals($product->price, $producta->price);
    }

}
