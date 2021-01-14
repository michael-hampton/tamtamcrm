<?php

namespace App\Repositories;

use App\Models\Design;
use App\Repositories\Base\BaseRepository;

/**
 * Class DesignRepository
 * @package App\Repositories
 */
class DesignRepository extends BaseRepository
{

    /**
     * CustomerRepository constructor.
     * @param Design $design
     */
    public function __construct(Design $design)
    {
        parent::__construct($design);
        $this->model = $design;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return Design
     */
    public function findDesignById(int $id): Design
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param Design $design
     * @param array $data
     * @return Design|null
     */
    public function save(Design $design, array $data): ?Design
    {
        $design->fill($data);
        $design->save();
    }

}
