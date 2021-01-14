<?php

namespace App\Components\Product;

use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Repositories\AttributeValueRepository;
use App\Repositories\ProductRepository;
use App\Traits\UploadableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CreateProduct
{
    use UploadableTrait;

    private ProductRepository $product_repo;

    private Product $product;

    private array $data;

    /**
     * Create a new job instance.
     *
     * @param ProductRepository $product_repo
     * @param array $data
     * @param Product $product
     */
    public function __construct(ProductRepository $product_repo, array $data, Product $product)
    {
        $this->product_repo = $product_repo;
        $this->data = $data;
        $this->product = $product;
    }

    public function execute()
    {
        $this->data['slug'] = Str::slug($this->data['name']);

        if (!empty($this->data['cover']) && $this->data['cover'] instanceof UploadedFile) {
            $this->data['cover'] = $this->saveCoverImage($this->data['cover']);
        }

        $this->data['is_featured'] = !empty($this->data['is_featured']) && $this->data['is_featured'] === 'true' ? 1 : 0;

        $this->product = $this->product_repo->save($this->data, $this->product);

        if (!empty($this->data['features'])) {
            $this->saveProductFeatures($this->product, $this->data['features']);
        }

        if (isset($this->data['image']) && !empty($this->data['image'])) {
            $this->saveProductImages(collect($this->data['image']), $this->product);
        }

        $this->saveCategories();

        if (!empty($this->data['variations'])) {
            $this->saveVariations($this->data['variations']);
        }

        return $this->product;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file): string
    {
        return $file->store('products', ['disk' => 'public']);
    }

    /**
     * @param Product $product
     * @param $fields
     * @return bool
     */
    private function saveProductFeatures(Product $product, $fields): bool
    {
        $features = json_decode($fields, true);

        if (empty($features)) {
            return true;
        }

        $product->features()->forceDelete();

        foreach ($features as $feature) {
            $product->features()->create($feature);
        }

        return true;
    }

    /**
     * @param Support $collection
     * @param Product $product
     * @return bool
     */
    public function saveProductImages($collection, Product $product): bool
    {
        $collection->each(
            function (UploadedFile $file) use ($product) {
                $filename = $this->storeFile($file);
                $productImage = new ProductImage(
                    [
                        'product_id' => $product->id,
                        'src'        => $filename
                    ]
                );
                $product->images()->save($productImage);
            }
        );

        return true;
    }

    private function saveCategories()
    {
        if (isset($this->data['category']) && !empty($this->data['category'])) {
            $categories = !is_array($this->data['category']) ? explode(
                ',',
                $this->data['category']
            ) : $this->data['category'];
            $this->product_repo->syncCategories($categories, $this->product);
            return true;
        }

        $this->product_repo->detachCategories($this->product);

        return true;
    }

    private function saveVariations($fields): bool
    {
        $variations = json_decode($fields, true);

        if (empty($variations)) {
            return true;
        }

        $this->product->attributes()->forceDelete();

        foreach ($variations as $variation) {
            $hasDefault = $this->product_repo->listProductAttributes()->where('default', 1)->count();
            $variation['is_default'] = $variation['is_default'] == 1 && $hasDefault > 0 ? 0 : 1;

            $objProductAttribute = new ProductAttribute();
            $objProductAttribute->fill($variation);

            $productAttribute = $this->product_repo->saveProductAttributes(
                $objProductAttribute,
                $this->product
            );

            foreach ($variation['attribute_values'] as $value) {
                $attribute = (new AttributeValueRepository(new AttributeValue))->find($value);
                $this->product_repo->saveCombination($productAttribute, $attribute);
            }
        }

        return true;
    }
}
