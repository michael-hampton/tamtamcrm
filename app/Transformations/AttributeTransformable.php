<?php

namespace App\Transformations;

use App\Models\Attribute;

class AttributeTransformable
{

    /**
     * @param Attribute $attribute
     * @return array
     */
    public function transformAttribute(Attribute $attribute)
    {
        return [
            'id'          => (int)$attribute->id,
            'name'        => (string)$attribute->name,
            'created_at'  => $attribute->created_at,
            'updated_at'  => $attribute->updated_at,
            'archived_at' => $attribute->deleted_at,
            'values'      => $attribute->values->count() > 0 ? $attribute->values : []
        ];
    }
}