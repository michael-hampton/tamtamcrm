<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProductAttributeRepositoryInterface extends BaseRepositoryInterface
{

    /**
     *
     * @param int $id
     */
    public function findProductAttributeById(int $id);
}
