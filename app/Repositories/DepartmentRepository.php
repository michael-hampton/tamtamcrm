<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{

    /**
     * @var Department
     */
    protected $model;

    /**
     * DepartmentRepository constructor.
     * @param Department $department
     */
    public function __construct(Department $department)
    {
        parent::__construct($department);
        $this->model = $department;
    }

    /**
     * List all Departments
     *
     * @param string $order
     * @param string $sort
     * @return Collection
     */
    public function listDepartments(string $order = 'id', string $sort = 'desc'): Collection
    {
        return $this->all(['*'], $order, $sort);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteDepartment(): bool
    {
        return $this->delete();
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchDepartment(string $text = null): Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchDepartment($text)->get();
    }

    public function save(array $data, Department $department): Department
    {
        $department->fill($data);

        $data['parent'] = $data['parent'] ?? 0;

        if ((int)$data['parent'] == 0) {
            $department->saveAsRoot();
        } else {
            $parent = $this->findDepartmentById($data['parent']);
            $department->parent()->associate($parent);
        }

        $department->save();

        return $department->fresh();
    }

    /**
     * @param int $id
     *
     * @return Department
     * @throws DepartmentNotFoundErrorException
     */
    public function findDepartmentById(int $id): Department
    {
        return $this->findOneOrFail($id);
    }

}
