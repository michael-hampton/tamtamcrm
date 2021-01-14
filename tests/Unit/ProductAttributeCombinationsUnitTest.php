<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Repositories\AttributeValueRepository;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductAttributeCombinationsUnitTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_create_a_product_attribute_combinations()
    {
        /**
         * Create:
         *  - Attribute: Sizes
         *    - Attribute value: small
         *  - Attribute: Colors
         *    - Attribute value: red
         */

        $attributeValue1 = new AttributeValue(['value' => 'small']);
        $attributeValueRepo1 = new AttributeValueRepository($attributeValue1);

        $attribute1 = Attribute::factory()->create(['name' => 'Sizes']);
        $createdValue1 = $attributeValueRepo1->associateToAttribute($attribute1);

        $attributeValue2 = new AttributeValue(['value' => 'red']);
        $attributeValueRepo2 = new AttributeValueRepository($attributeValue2);

        $attribute2 = Attribute::factory()->create(['name' => 'Colors']);
        $createdValue2 = $attributeValueRepo2->associateToAttribute($attribute2);

        $data = [
            'quantity' => 2,
            'price'    => 2.50
        ];

        $productAttribute = new ProductAttribute($data);
        $product = Product::factory()->create();

        $productRepo = new ProductRepository($product);
        $created = $productRepo->saveProductAttributes($productAttribute, $product);

        $combinations = $productRepo->saveCombination($created, $createdValue1, $createdValue2);

        $this->assertCount(2, $combinations);
    }
}