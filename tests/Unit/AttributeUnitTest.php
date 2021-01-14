<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttributeUnitTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /** @test */
    public function it_should_error_when_the_attribute_is_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $attributeRepo = new AttributeRepository(new Attribute);
        $attributeRepo->findAttributeById(999);
    }

    /** @test */
    public function it_should_show_the_attribute()
    {
        $attribute = Attribute::factory()->create();

        $attributeRepo = new AttributeRepository(new Attribute);
        $found = $attributeRepo->findAttributeById($attribute->id);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attribute->name, $found->name);
    }

    /** @test */
    /*public function it_should_list_all_the_attributes()
    {
        factory(Attribute::class, 5)->create();

        $attributeRepo = new AttributeRepository(new Attribute);
        $list = $attributeRepo->listAttributes();

        $this->assert(5, $list);
    }*/

    /** @test */
    public function it_will_return_null_when_deleting_attribute_that_is_not_created_yet()
    {
        $attributeRepo = new AttributeRepository(new Attribute);
        $delete = $attributeRepo->deleteAttribute();

        $this->assertNull($delete);
    }

    /** @test */
    public function it_can_delete_the_attribute()
    {
        $attribute = Attribute::factory()->create();

        $attributeRepo = new AttributeRepository($attribute);
        $delete = $attributeRepo->deleteAttribute();

        $this->assertTrue($delete);
    }

    /** @test */
    /* public function it_errors_when_updating_attribute()
    {
        $this->expectException(UpdateAttributeErrorException::class);

        $attribute = factory(Attribute::class)->create();

        $attributeRepo = new AttributeRepository($attribute);
        $attributeRepo->updateAttribute(['name' => null]);
    }*/

    /** @test */
    public function it_can_update_the_attribute()
    {
        $attribute = Attribute::factory()->create();

        $data = [
            'name' => $this->faker->word
        ];

        $attributeRepo = new AttributeRepository($attribute);
        $attribute = $attributeRepo->save($attribute, $data);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($data['name'], $attribute->name);
    }

    /** @test */
    public function it_can_create_attribute()
    {
        $data = [
            'name' => $this->faker->word
        ];

        $objAttribute = new Attribute;
        $attributeRepo = new AttributeRepository($objAttribute);
        $attribute = $attributeRepo->save($objAttribute, $data);

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($data['name'], $attribute->name);
    }
}