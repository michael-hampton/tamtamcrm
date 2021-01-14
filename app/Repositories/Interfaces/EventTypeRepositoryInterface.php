<?php

namespace App\Repositories\Interfaces;

use App\Models\EventType;

interface EventTypeRepositoryInterface
{
    public function getAll();

    /**
     *
     * @param int $id
     * @return EventType
     * @return EventType
     */
    public function findEventTypeById(int $id): EventType;
}
