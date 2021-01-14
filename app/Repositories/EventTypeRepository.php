<?php

namespace App\Repositories;

use App\Models\EventType;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\EventTypeRepositoryInterface;
use Exception;

class EventTypeRepository extends BaseRepository implements EventTypeRepositoryInterface
{

    /**
     * EventTypeRepository constructor.
     *
     * @param EventType $eventType
     */
    public function __construct(EventType $eventType)
    {
        parent::__construct($eventType);
        $this->model = $eventType;
    }

    public function getAll()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param int $id
     *
     * @return EventType
     * @throws Exception
     */
    public function findEventTypeById(int $id): EventType
    {
        return $this->findOneOrFail($id);
    }
}
