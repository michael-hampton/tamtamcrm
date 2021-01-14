<?php

namespace Tests\Unit;

use App\Components\Product\CreateProduct;
use App\Factory\ProductFactory;
use App\Models\Account;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Requests\SearchRequest;
use App\Search\ProductSearch;
use App\Transformations\ProductTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductTest extends TestCase
{

    use DatabaseTransactions, ProductTransformable, WithFaker;

    private $user;

    private $company;

    /**
     * @var int
     */
    private $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->account = Account::where('id', 1)->first();
        $this->user = User::factory()->create();;
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function it_can_return_the_product_of_the_cover_image()
    {
        $thumbnails = [
            UploadedFile::fake()->image('cover.jpg', 600, 600),
            UploadedFile::fake()->image('cover.jpg', 600, 600),
            UploadedFile::fake()->image('cover.jpg', 600, 600)
        ];
        $collection = collect($thumbnails);
        $product = Product::factory()->create();
        $productRepo = new ProductRepository($product);
        (new CreateProduct($productRepo, [], $product))->saveProductImages($collection, $product);
        $images = $productRepo->findProductImages($product);

        $images->each(
            function (ProductImage $image) use ($product) {
                $productImageRepo = new ProductImageRepository($image);
                $foundProduct = $productImageRepo->findProduct();
                $this->assertInstanceOf(Product::class, $foundProduct);
                $this->assertEquals($product->name, $foundProduct->name);
                $this->assertEquals($product->slug, $foundProduct->slug);
                $this->assertEquals($product->description, $foundProduct->description);
                $this->assertEquals($product->quantity, $foundProduct->quantity);
                $this->assertEquals($product->price, $foundProduct->price);
                $this->assertEquals($product->status, $foundProduct->status);

                if (File::exists(public_path($image->src))) {
                    File::delete(public_path($image->src));
                }
            }
        );
    }

    /** @test */
    public function it_can_save_the_thumbnails_properly_in_the_file_storage()
    {
        $thumbnails = [
            UploadedFile::fake()->image('cover.jpg', 600, 600),
            UploadedFile::fake()->image('cover.jpg', 600, 600),
            UploadedFile::fake()->image('cover.jpg', 600, 600)
        ];
        $collection = collect($thumbnails);
        $product = Product::factory()->create();
        $productRepo = new ProductRepository($product);
        (new CreateProduct($productRepo, [], $product))->saveProductImages($collection, $product);
        $images = $productRepo->findProductImages($product);

        $images->each(
            function (ProductImage $image) {
                $exists = Storage::disk('public')->exists($image->src);
                $this->assertTrue($exists);
                File::delete(public_path($image->src));
            }
        );
    }

    /** @test */
    public function it_can_save_the_cover_image_properly_in_file_storage()
    {
        $cover = UploadedFile::fake()->image('cover.jpg', 600, 600);
        $product = Product::factory()->create();
        $productRepo = new ProductRepository($product);
        $filename = (new CreateProduct($productRepo, [], $product))->saveCoverImage($cover);
        $exists = Storage::disk('public')->exists($filename);
        $this->assertTrue($exists);

        if ($exists) {
            File::delete(public_path($filename));
        }
    }

    /** @test */
    public function it_errors_when_the_slug_in_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $product = new ProductRepository(new Product);
        $product->findProductBySlug('unknown');
    }

    public function it_errors_creating_the_product_when_required_fields_are_not_passed()
    {
        $product = new Product();
        $this->expectException(QueryException::class);
        $task = new ProductRepository($product);
        $task->save([], $product);
    }

    /** @test */
    public function it_can_find_the_product_with_the_slug()
    {
        $product = Product::factory()->create();
        $productRepo = new ProductRepository(new Product);
        $found = $productRepo->findProductBySlug($product->slug);
        $this->assertEquals($product->name, $found->name);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create();
        $deleted = $product->deleteEntity();
        $this->assertTrue($deleted);
        //$this->assertDatabaseMissing('products', ['name' => $product->name]);
    }

    /** @test */
    public function it_can_list_all_the_products()
    {
        $product = Product::factory()->create();
        $attributes = $product->getFillable();
        $products =
            (new ProductSearch(new ProductRepository(new Product)))->filter(new SearchRequest(), $this->account);
        $this->assertNotEmpty($products);
    }

    /** @test */
    public function it_errors_finding_a_product()
    {
        $this->expectException(ModelNotFoundException::class);
        $product = new ProductRepository(new Product);
        $product->findProductById(999);
    }

    /** @test */
    public function it_can_find_the_product()
    {
        $product = Product::factory()->create();
        $productRepo = new ProductRepository(new Product);
        $found = $productRepo->findProductById($product->id);
        $this->assertInstanceOf(Product::class, $found);
        $this->assertEquals($product->sku, $found->sku);
        $this->assertEquals($product->name, $found->name);
        $this->assertEquals($product->slug, $found->slug);
        $this->assertEquals($product->description, $found->description);
        $this->assertEquals($product->price, $found->price);
        $this->assertEquals($product->status, $found->status);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $product = Product::factory()->create();
        $productName = 'apple';
        $data = [
            'account_id'  => $this->account->id,
            'user_id'     => $this->user->id,
            'sku'         => '11111',
            'name'        => $productName,
            'slug'        => Str::slug($productName),
            'description' => $this->faker->paragraph,
            'price'       => 9.95,
            'status'      => 1
        ];
        $productRepo = new ProductRepository($product);
        $updated = (new CreateProduct($productRepo, $data, $product))->execute();
        $this->assertInstanceOf(Product::class, $updated);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $product = (new ProductFactory())->create($this->user, $this->account);
        $company = Company::factory()->create();

        $name = $this->faker->word;

        $params = [
            'company_id'  => $company->id,
            'sku'         => $this->faker->numberBetween(1111111, 999999),
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->paragraph,
            'price'       => 9.95,
            'status'      => 1,
        ];
        $productRepo = new ProductRepository(new Product);
        $created = (new CreateProduct($productRepo, $params, $product))->execute();
        $this->assertInstanceOf(Product::class, $created);
        $this->assertEquals($params['sku'], $created->sku);
        $this->assertEquals($params['name'], $created->name);
        $this->assertEquals($params['slug'], $created->slug);
        $this->assertEquals($params['description'], $created->description);
        $this->assertEquals($params['price'], $created->price);
        $this->assertEquals($params['status'], $created->status);
    }

    /** @test */
    public function it_can_delete_a_thumbnail_image()
    {
        $product_name = 'apple';
        $cover = UploadedFile::fake()->image('file.png', 600, 600);
        $params = [
            'account_id'  => $this->account->id,
            'sku'         => $this->faker->numberBetween(1111111, 999999),
            'name'        => $product_name,
            'slug'        => Str::slug($product_name),
            'description' => $this->faker->paragraph,
            'cover'       => $cover,
            'quantity'    => 10,
            'price'       => 9.95,
            'company_id'  => $this->company->id,
            'status'      => 1,
            'image'       => [
                UploadedFile::fake()->image('file.png', 200, 200),
                UploadedFile::fake()->image('file1.png', 200, 200),
                UploadedFile::fake()->image('file2.png', 200, 200)
            ]
        ];
        $productRepo = new ProductRepository(new Product);
        $product = (new ProductFactory())->create($this->user, $this->account);
        $created = (new CreateProduct($productRepo, $params, $product))->execute();
        //$repo->saveProductImages(collect($params['image']), $created);
        $thumbnails = $productRepo->findProductImages($created);
        $this->assertCount(3, $thumbnails);

        $thumbnails->each(
            function ($thumbnail) {
                $repo = new ProductRepository(new Product());
                $repo->deleteThumb($thumbnail->src);

                if (File::exists(public_path($thumbnail->src))) {
                    File::delete(public_path($thumbnail->src));
                }
            }
        );

        $this->assertCount(0, $productRepo->findProductImages($created));

        if (File::exists(public_path($created->cover))) {
            File::delete(public_path($created->cover));
        }
    }

    /** @test */
    public function it_can_show_all_the_product_images()
    {
        $product_name = 'apple';
        $cover = UploadedFile::fake()->image('file.png', 600, 600);
        $params = [
            'account_id'  => $this->account->id,
            'sku'         => $this->faker->numberBetween(1111111, 999999),
            'name'        => $product_name,
            'slug'        => Str::slug($product_name),
            'description' => $this->faker->paragraph,
            'cover'       => $cover,
            'quantity'    => 10,
            'price'       => 9.95,
            'status'      => 1,
            'company_id'  => $this->company->id,
            'image'       => [
                UploadedFile::fake()->image('file.png', 200, 200),
                UploadedFile::fake()->image('file1.png', 200, 200),
                UploadedFile::fake()->image('file2.png', 200, 200)
            ]
        ];

        $product = (new ProductFactory())->create($this->user, $this->account);
        $productRepo = new ProductRepository(new Product);
        $created = (new CreateProduct($productRepo, $params, $product))->execute();
        $repo = new ProductRepository($created);
        //$repo->saveProductImages(collect($params['image']), $created);
        $this->assertCount(3, $repo->findProductImages($created));

        if (File::exists(public_path($created->cover))) {
            File::delete(public_path($created->cover));
        }

        foreach ($created->images as $image) {
            if (File::exists(public_path($image->src))) {
                File::delete(public_path($image->src));
            }
        }
    }

    /** @test */
    public function it_can_delete_the_file_only_by_updating_the_database()
    {
        $product = Product::factory()->create();
        $productRepo = new ProductRepository($product);
        $this->assertTrue($productRepo->deleteFile(['product' => $product->id]));
    }

    /**
     * @test
     */
    public function testInventoryReduction()
    {
        $product = Product::factory()->create(['quantity' => 5]);
        $product->reduceQuantityAvailiable(1);
        $this->assertEquals($product->quantity, 4);
    }


    /** @test */
    public function it_can_detach_all_the_categories()
    {
        $product = Product::factory()->create();
        $categories = Category::factory()->count(4)->create();
        $ids = $categories->pluck('id')->toArray();
        $productRepo = new ProductRepository($product);

        $productRepo->syncCategories($ids, $product);
        $this->assertCount(4, $productRepo->getCategories());
        $productRepo->detachCategories($product);
        $this->assertCount(0, $productRepo->getCategories());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
