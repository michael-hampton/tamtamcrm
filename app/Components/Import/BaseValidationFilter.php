<?php

namespace App\Components\Import;

abstract class BaseValidationFilter
{
    use NameableTrait;

    /**
     * No need to attach the filter to any fields, since it will receive full csv array line if true
     *
     * @var bool
     */
    public $global = false;

    /**
     * @param $value
     * @param $entity
     * @return mixed
     */
    abstract public function filter($value, $entity);
}
