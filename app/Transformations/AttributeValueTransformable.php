<?php

namespace App\Transformations;

use App\Models\AttributeValue;

class AttributeValueTransformable
{

    /**
     * @param AttributeValue $attribute_value
     * @return array
     */
    public function transformAttributeValue(AttributeValue $attribute_value)
    {
        return [
            'id'           => (int)$attribute_value->id,
            'attribute_id' => (int)$attribute_value->attribute_id,
            'attribute'    => (new AttributeTransformable)->transformAttribute($attribute_value->attribute),
            'value'        => (string)$attribute_value->value,
            'created_at'   => $attribute_value->created_at,
            'updated_at'   => $attribute_value->updated_at,
            'archived_at'  => $attribute_value->deleted_at,
        ];
    }
}