<?php

namespace App\Traits;

use App\Requests\SearchRequest;
use ReflectionClass;

trait EntityDataBuilder
{
    private array $errors = [];

    private string $entity_string = '';

    private $repository;

    private $entity_class;

    private $filter_class;

    public function buildEntityData($entity)
    {
        $this->setEntity($entity);
        $this->setRepositoryClass();

        if (count($this->errors) > 0) {
            return false;
        }

        if (!method_exists($this->repository, 'getAll')) {
            $this->errors[] = "Unable to filter";
            return false;
        }

        $search_request = new SearchRequest();
        $search_request->replace(['id' => $this->entity->id]);

        $data = $this->repository->getAll($search_request, $this->entity->account);

        if (empty($data)) {
            return false;
        }

        $entity_data = collect($data)->where('id', $this->entity->id)->first();

        if (empty($entity_data)) {
            return false;
        }

        return $entity_data;
    }

    public function setEntity($entity_class)
    {
        $this->entity_string = (new ReflectionClass($entity_class))->getShortName();
        $this->entity_class = $entity_class;
    }

    public function setRepositoryClass()
    {
        $repo = 'App\Repositories\\' . $this->entity_string . 'Repository';

        if (!class_exists($repo)) {
            $this->errors[] = 'Unable to find repo';
        }

        $this->repository = new $repo($this->entity);

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
