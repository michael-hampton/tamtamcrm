<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Repositories\AttributeRepository;
use App\Repositories\AttributeValueRepository;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductAttributeUnitTest extends TestCase
{

    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_throws_error_when_the_product_attribute_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $productAttributeRepo = new ProductAttributeRepository(new ProductAttribute);
        $productAttributeRepo->findProductAttributeById(999);
    }

    /** @test */
    public function it_can_find_the_product_attribute_by_id()
    {
        $product = Product::factory()->create();;
        $productAttribute = ProductAttribute::factory()->create(
            [
                'product_id' => $product->id
            ]
        );

        $productAttributeRepo = new ProductAttributeRepository(new ProductAttribute);
        $found = $productAttributeRepo->findProductAttributeById($productAttribute->id);

        $this->assertEquals($productAttribute->quantity, $found->quantity);
        $this->assertEquals($productAttribute->price, $found->price);
    }

    /** @test */
    public function it_can_sync_the_attribute_values_to_product_attributes()
    {
        $attribute = Attribute::factory()->create(['name' => $this->faker->word()]);

        $attributeValueRepo = new AttributeValueRepository(new AttributeValue);
        $created = $attributeValueRepo->createAttributeValue($attribute, ['value' => $this->faker->word()]);

        $attributeRepo = new AttributeRepository($attribute);
        $associated = $attributeRepo->associateAttributeValue($created);

        $this->assertInstanceOf(AttributeValue::class, $created);
        $this->assertInstanceOf(AttributeValue::class, $associated);
        $this->assertEquals($created->name, $associated->name);
    }

    /** @test */
    public function it_returns_null_deleting_non_existing_product_attribute()
    {
        $product = Product::factory()->create();;
        $productRepo = new ProductRepository($product);
        $deleted = $productRepo->removeProductAttribute(new ProductAttribute, $product);

        $this->assertFalse($deleted);
    }

    /** @test */
    public function it_can_remove_product_attribute()
    {
        $data = [
            'quantity' => 1,
            'price'    => 10.45
        ];

        $productAttribute = new ProductAttribute($data);

        $product = Product::factory()->create();;
        $productRepo = new ProductRepository($product);
        $created = $productRepo->saveProductAttributes($productAttribute, $product);

        $deleted = $productRepo->removeProductAttribute($created, $product);

        $this->assertTrue($deleted);
    }

    /** @test */
    public function it_can_create_product_attribute()
    {
        $data = [
            'quantity' => 1,
            'price'    => 10.45
        ];

        $productAttribute = new ProductAttribute($data);

        $product = Product::factory()->create();;
        $productRepo = new ProductRepository($product);
        $created = $productRepo->saveProductAttributes($productAttribute, $product);

        $this->assertInstanceOf(ProductAttribute::class, $created);
        $this->assertInstanceOf(Product::class, $productAttribute->product);

        $this->assertEquals($data['quantity'], $created->quantity);
        $this->assertEquals($data['price'], $created->price);
        $this->assertEquals($product->name, $created->product->name);
        $this->assertEquals($product->price, $created->product->price);
    }
}
