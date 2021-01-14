<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface NotificationRepositoryInterface
{
    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listNotifications(
        $columns = array('*'),
        string $orderBy = 'id',
        string $sortBy = 'asc'
    ): Collection;
}
