<?php

namespace App\Repositories;

use App\Models\Domain;

class DomainRepository
{
    private $model;

    public function __construct(Domain $domain)
    {
        $this->model = $domain;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
