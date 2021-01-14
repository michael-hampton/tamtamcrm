<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface EventRepositoryInterface extends BaseRepositoryInterface
{
    //public function getAll(string $orderBy, string $orderDir, int $recordsPerPage, $blActive = true);

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listEvents($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;

    /**
     *
     */
    public function deleteEvent(): bool;

    /**
     *
     * @param int $id
     * @return Event
     * @return Event
     */
    public function findEventById(int $id): Event;

    /**
     *
     * @param Task $objTask
     * @return Collection
     */
    public function getEventsForTask(Task $objTask): Collection;

    /**
     *
     * @param User $objUser
     * @param int $account_id
     * @return Collection
     */
    public function getEventsForUser(User $objUser, int $account_id): Collection;
}
