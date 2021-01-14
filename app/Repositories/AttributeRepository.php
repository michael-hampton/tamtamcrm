<?php

namespace App\Repositories;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Repositories\Base\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AttributeRepository extends BaseRepository
{
    /**
     * @var Attribute
     */
    protected $model;

    /**
     * AttributeRepository constructor.
     * @param Attribute $attribute
     */
    public function __construct(Attribute $attribute)
    {
        parent::__construct($attribute);
        $this->model = $attribute;
    }

    /**
     * @param Attribute $attribute
     * @param array $data
     * @return Attribute
     */
    public function save(Attribute $attribute, array $data): Attribute
    {
        $attribute->fill($data);
        $attribute->save();
        return $attribute;
    }

    /**
     * @param int $id
     * @return Attribute
     */
    public function findAttributeById(int $id): Attribute
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool|null
     * @throws Exception
     */
    public function deleteAttribute(): ?bool
    {
        return $this->model->delete();
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listAttributes($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    /**
     * @return Collection
     */
    public function listAttributeValues(): Collection
    {
        return $this->model->values()->get();
    }

    /**
     * @param AttributeValue $attributeValue
     * @return bool|false|Model
     */
    public function associateAttributeValue(AttributeValue $attributeValue)
    {
        return $this->model->values()->save($attributeValue);
    }

    /**
     * Gets the class name.
     *
     * @return     string The class name.
     */
    public function getModel()
    {
        return $this->model;
    }
}
