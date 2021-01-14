<?php

namespace App\Components\Import\ValidationFilters;

use App\Components\Import\BaseValidationFilter;

class NumberValidationFilter extends BaseValidationFilter
{
    /**
     * @var string
     */
    protected $name = 'number_validation';

    /**
     * @param mixed $value
     * @return bool
     */
    public function filter($value, $entity)
    {
        if (empty($value)) {
            return true;
        }

        $class = 'App\Models\\' . $entity;

        $object = $class::where('number', '=', $value)->first();

        return empty($object);
    }
}
