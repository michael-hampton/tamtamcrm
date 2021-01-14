<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Repositories\AttributeValueRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttributeValueUnitTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_can_be_dissociated_from_the_attribute()
    {
        $attributeValue = new AttributeValue(['value' => 'small']);
        $attributeValueRepo = new AttributeValueRepository($attributeValue);

        $attribute = Attribute::factory()->create();
        $createdValue = $attributeValueRepo->associateToAttribute($attribute);

        $attributeValueRepo2 = new AttributeValueRepository($createdValue);
        $removedAttribute = $attributeValueRepo2->dissociateFromAttribute();

        $this->assertTrue($removedAttribute);
    }

    /** @test */
    public function it_can_be_associated_with_the_attribute()
    {
        $attributeValue = new AttributeValue(['value' => 'sizes']);
        $attributeValueRepo = new AttributeValueRepository($attributeValue);

        $attribute = Attribute::factory()->create();
        $attributeValueRepo->associateToAttribute($attribute);

        $this->assertCount(1, $attribute->values->all());
    }
}