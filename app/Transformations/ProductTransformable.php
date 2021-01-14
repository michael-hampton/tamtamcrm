<?php

namespace App\Transformations;

use App\Models\File;
use App\Models\Product;
use App\Models\ProductAttribute;

trait ProductTransformable
{

    /**
     * @param Product $product
     * @return array
     */
    protected function transformProduct(Product $product)
    {
        return [
            'id'             => (int)$product->id,
            'account_id'     => (int)$product->account_id,
            'rating'         => (int)$product->rating,
            'ratings_count'  => (int)$product->ratings_count,
            'reviews'        => $product->reviews,
            'name'           => $product->name,
            'sku'            => $product->sku,
            'created_at'     => $product->created_at,
            'deleted_at'     => $product->deleted_at,
            'slug'           => $product->slug,
            'description'    => $product->description,
            'cost'           => (float)$product->cost ?: 0,
            'user_id'        => (int)$product->user_id,
            'assigned_to'    => (int)$product->assigned_to,
            'notes'          => $product->notes ?: '',
            'price'          => (float)$product->price ?: 0,
            'quantity'       => (int)$product->quantity ?: 1,
            'reserved_stock' => (int)$product->reserved_stock ?: 0,
            'length'         => (float)$product->length ?: 0,
            'width'          => (float)$product->width ?: 0,
            'height'         => (float)$product->height ?: 0,
            'weight'         => (float)$product->weight ?: 0,
            'mass_unit'      => (string)$product->mass_unit ?: '',
            'distance_unit'  => (string)$product->distance_unit ?: '',
            'status'         => $product->status,
            'features'       => $product->features,
            'cover'          => $product->cover,
            'brand_id'       => (int)$product->brand_id,
            'company_id'     => (int)$product->company_id,
            'brand'          => !empty($product->company) ? $product->company->name : null,
            'is_featured'    => (bool)$product->is_featured,
            'category_ids'   => $product->categories()->pluck('category_id')->all(),
            'images'         => $product->images()->get(['src']),
            'attributes'     => $product->attributes->count() > 0 ? $this->transformProductAttributes(
                $product->attributes
            ) : [],
            'custom_value1'  => $product->custom_value1 ?: '',
            'custom_value2'  => $product->custom_value2 ?: '',
            'custom_value3'  => $product->custom_value3 ?: '',
            'custom_value4'  => $product->custom_value4 ?: '',
            'files'          => $this->transformProductFiles($product->files),
            'is_deleted'     => (bool)$product->is_deleted,
        ];
    }

    private function transformProductAttributes($productAttributes)
    {
        if ($productAttributes->count() === 0) {
            return [];
        }

        return $productAttributes->map(
            function (ProductAttribute $product_attribute) {
                return (new ProductAttributeTransformable)->transformProductAttribute($product_attribute);
            }
        )->all();
    }

    private function transformProductFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }
}
