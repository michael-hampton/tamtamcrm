<?php

namespace App\Repositories;

use App\Models\Account;
use App\Models\CaseCategory;
use App\Repositories\Base\BaseRepository;
use Exception;
use Illuminate\Support\Collection;

class CaseCategoryRepository extends BaseRepository
{
    /**
     * CaseCategoryRepository constructor.
     * @param CaseCategory $category
     */
    public function __construct(CaseCategory $category)
    {
        parent::__construct($category);
        $this->model = $category;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param CaseCategory $case_category
     * @return CaseCategory
     */
    public function create(array $data, CaseCategory $case_category)
    {
        $case_category->fill($data);
        $case_category->save();

        return $case_category;
    }

    /**
     * @param array $data
     * @param CaseCategory $case_category
     * @return CaseCategory
     */
    public function update(array $data, CaseCategory $case_category)
    {
        $case_category->update($data);

        return $case_category;
    }

    /**
     * @param int $id
     * @return CaseCategory
     */
    public function findCategoryById(int $id): CaseCategory
    {
        return $this->findOneOrFail($id);
    }

}
