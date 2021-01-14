<?php

namespace App\Repositories;

use App\Models\ErrorLog;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;

class ErrorLogRepository extends BaseRepository
{

    /**
     * EmailRepository constructor.
     * @param ErrorLog $error_log
     */
    public function __construct(ErrorLog $error_log)
    {
        parent::__construct($error_log);
        $this->model = $error_log;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     *
     * @return ErrorLog
     */
    public function findErrorLogById(int $id): ErrorLog
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listErrorLogs($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }


    /**
     * @param array $data
     * @return ErrorLog|null
     */
    public function save(array $data): ?ErrorLog
    {
        $error_log = new ErrorLog();
        $error_log->fill($data);
        $error_log->save();


        return $error_log->fresh();
    }
}
