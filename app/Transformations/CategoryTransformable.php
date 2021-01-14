<?php

namespace App\Transformations;

use App\Models\Category;

trait CategoryTransformable
{

    /**
     * @param Category $category
     * @return array
     */
    protected function transformCategory(Category $category)
    {
        return [
            'id'          => (int)$category->id,
            'name'        => $category->name,
            'cover'       => $category->cover,
            'description' => $category->description,
            'slug'        => $category->slug,
            'parent_id'   => $category->parent_id,
            'status'      => $category->status,
        ];
    }

}
