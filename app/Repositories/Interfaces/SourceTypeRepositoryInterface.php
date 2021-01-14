<?php

namespace App\Repositories\Interfaces;

use App\Models\SourceType;

interface SourceTypeRepositoryInterface
{

    public function getAll();

    /**
     *
     * @param int $id
     * @return SourceType
     * @return SourceType
     */
    public function findSourceById(int $id): SourceType;
}
