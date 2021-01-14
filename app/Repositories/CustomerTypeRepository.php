<?php

namespace App\Repositories;

use App\Models\CustomerType;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CustomerTypeRepositoryInterface;
use Exception;

class CustomerTypeRepository extends BaseRepository implements CustomerTypeRepositoryInterface
{

    /**
     * CustomerTypeRepository constructor.
     *
     * @param CustomerType $customerType
     */
    public function __construct(CustomerType $customerType)
    {
        parent::__construct($customerType);
        $this->model = $customerType;
    }

    public function getAll()
    {
        return $this->model->orderBy('name', 'asc')->get();
    }

    /**
     * @param int $id
     *
     * @return CustomerType
     * @throws Exception
     */
    public function findCustomerTypeById(int $id): CustomerType
    {
        return $this->findOneOrFail($id);
    }
}
