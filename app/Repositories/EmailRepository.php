<?php

namespace App\Repositories;

use App\Models\Email;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;

class EmailRepository extends BaseRepository
{

    /**
     * EmailRepository constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        parent::__construct($email);
        $this->model = $email;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     *
     * @return Email
     */
    public function findEmailById(int $id): Email
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
    public function listEmails($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }


    /**
     * @param array $data
     * @param Email $email
     * @return Email|null
     */
    public function save(array $data, Email $email): ?Email
    {
        $email->fill($data);
        $email->save();


        return $email->fresh();
    }
}
