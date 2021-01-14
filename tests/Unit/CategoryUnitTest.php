<?php

namespace Tests\Unit;

use App\Factory\CategoryFactory;
use App\Models\Account;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CategoryUnitTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    /**
     * @var Account
     */
    private Account $account;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->account = Account::where('id', 1)->first();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_the_child_categories()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(
            [
                'parent_id' => $parent->id
            ]
        );
        $categoryRepo = new CategoryRepository($parent);
        $children = $categoryRepo->findChildren();
        foreach ($children as $c) {
            $this->assertInstanceOf(Category::class, $c);
            $this->assertEquals($child->id, $c->id);
        }
    }

    /** @test */
    public function it_can_get_the_parent_category()
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(
            [
                'parent_id' => $parent->id
            ]
        );
        $categoryRepo = new CategoryRepository($child);
        $found = $categoryRepo->findParentCategory();
        $this->assertInstanceOf(Category::class, $found);
        $this->assertEquals($parent->id, $child->parent_id);
    }

    /** @test */
    public function it_can_return_products_in_the_category()
    {
        $category = Category::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $product = Product::factory()->create();
        $categoryRepo->syncProducts([$product->id]);
        $products = $categoryRepo->findProducts();
        foreach ($products as $producta) {
            $this->assertEquals($product->id, $producta->id);
        }
    }

    /** @test */
    public function it_errors_looking_for_the_category_if_the_slug_is_not_found()
    {
        $category = Category::factory()->create();
        $this->expectException(ModelNotFoundException::class);
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->findCategoryBySlug('unknown', $this->account);
    }

    /** @test */
    public function it_can_get_the_category_by_slug()
    {
        $category = Category::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $cat = $categoryRepo->findCategoryBySlug($category->slug, $this->account);
        $this->assertEquals($category->name, $cat->name);
    }

    /** @test */
    public function it_can_delete_file_only_in_the_database()
    {
        $category = Category::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->deleteFile(['category' => $category->id]);
        $this->assertDatabaseHas('categories', ['cover' => null]);
    }

    /** @test */
    public function it_can_detach_the_products()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->syncProducts([$product->id]);
        $categoryRepo->detachProducts();
        $products = $categoryRepo->findProducts();
        $this->assertCount(0, $products);
    }

    /** @test */
    public function it_can_sync_products_in_the_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->syncProducts([$product->id]);
        $products = $categoryRepo->findProducts();
        foreach ($products as $producta) {
            $this->assertEquals($product->name, $producta->name);
        }
    }

    /** @test */
//    public function it_errors_creating_the_category_when_required_fields_are_not_passed() {
//        $this->expectException(\Illuminate\Database\QueryException::class);
//        $product = new CategoryRepository(new CategorySearch);
//        $product->createCategory([]);
//    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $category = Category::factory()->create();
        $categoryRepo = new CategoryRepository($category);
        $categoryRepo->deleteCategory();
        $this->assertDatabaseMissing('categories', collect($category)->all());
    }

    /** @test */
    public function it_can_list_all_the_categories()
    {
        $category = Category::factory()->create();
        $attributes = $category->getFillable();
        $categoryRepo = new CategoryRepository(new Category);
        $categories = $categoryRepo->listCategories('id', 'desc', $this->account);
        $categories->each(
            function ($category, $key) use ($attributes) {
                foreach ($category->getFillable() as $key => $value) {
                    $this->assertArrayHasKey($key, $attributes);
                }
            }
        );
    }

    /** @test */
    public function it_errors_finding_a_category()
    {
        $this->expectException(ModelNotFoundException::class);
        $category = new CategoryRepository(new Category);
        $category->findCategoryById(999);
    }

    /** @test */
    public function it_can_find_the_category()
    {
        $category = Category::factory()->create();
        $categoryRepo = new CategoryRepository(new Category);
        $found = $categoryRepo->findCategoryById($category->id);
        $this->assertEquals($category->name, $found->name);
        $this->assertEquals($category->slug, $found->slug);
        $this->assertEquals($category->description, $found->description);
        //$this->assertEquals($this->category->cover, $found->cover);
        $this->assertEquals($category->status, $found->status);
    }

    /** @test */
    public function it_can_update_the_category()
    {
        $category = Category::factory()->create();
        $cover = UploadedFile::fake()->image('file.png', 600, 600);
        //$parent = CategorySearch::factory()->create();
        $params = [
            'name'        => 'Boys',
            'slug'        => 'boys',
            'description' => $this->faker->paragraph,
            'status'      => 1,
            'parent'      => 0,
            'cover'       => $cover
        ];
        $categoryRepo = new CategoryRepository($category);
        $updated = $categoryRepo->updateCategory($params, $category);
        $this->assertInstanceOf(Category::class, $updated);
        $this->assertEquals($params['name'], $updated->name);
        $this->assertEquals($params['slug'], $updated->slug);
        $this->assertEquals($params['description'], $updated->description);
        $this->assertEquals($params['status'], $updated->status);
        $this->assertEquals($params['parent'], $updated->parent_id);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $cover = UploadedFile::fake()->image('file.png', 600, 600);
        $parent = Category::factory()->create();
        $params = [
            'name'        => 'Boys',
            'slug'        => 'boys',
            'cover'       => $cover,
            'description' => $this->faker->paragraph,
            'status'      => 1,
            'parent'      => $parent->id
        ];
        $categoryRepo = new CategoryRepository(new Category);
        $category = CategoryFactory::create($this->account, $this->user);
        $created = $categoryRepo->createCategory($params, $category);
        $this->assertInstanceOf(Category::class, $created);
        $this->assertEquals($params['name'], $created->name);
        $this->assertEquals($params['slug'], $created->slug);
        $this->assertEquals($params['description'], $created->description);
        $this->assertEquals($params['status'], $created->status);
        $this->assertEquals($params['parent'], $created->parent_id);
    }

    /** @test */
    public function it_can_create_root_category()
    {
        $params = [
            'name'        => 'Boys',
            'slug'        => 'boys',
            'description' => $this->faker->paragraph,
            'status'      => 1
        ];

        $category = CategoryFactory::create($this->account, $this->user);
        $categoryRepo = new CategoryRepository($category);
        $created = $categoryRepo->createCategory($params, $category);
        $this->assertTrue($created->isRoot());
    }

    /** @test */
    public function it_can_update_child_category_to_root_category()
    {
        // suppose to have a child category
        $parent = Category::factory()->create();
        $child = Category::factory()->create();
        $child->parent()->associate($parent)->save();
        // send params without parent
        $categoryRepo = new CategoryRepository($child);
        $updated = $categoryRepo->updateCategory(
            [
                'name' => 'Boys',
                'slug' => 'boys'
            ],
            $child
        );
        // check if updated category is root
        $this->assertTrue($updated->isRoot());
    }

    /** @test */
    public function it_can_update_root_category_to_child()
    {
        $child = Category::factory()->create();
        $parent = Category::factory()->create();

        // set parent category via repository
        $category = new CategoryRepository($child);
        $updated = $category->updateCategory(
            [
                'name'   => 'Boys',
                'slug'   => 'boys',
                'parent' => $parent->id
            ],
            $child
        );
        // check if updated category is root
        $this->assertTrue($updated->parent->is($parent));
    }

}
