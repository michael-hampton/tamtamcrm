<?php

namespace App\Repositories;

use App\Models\ExpenseCategory;
use App\Repositories\Base\BaseRepository;

class ExpenseCategoryRepository extends BaseRepository
{
    /**
     * ExpenseCategoryRepository constructor.
     * @param ExpenseCategory $category
     */
    public function __construct(ExpenseCategory $category)
    {
        parent::__construct($category);
        $this->model = $category;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function create(array $data, ExpenseCategory $expense_category): ExpenseCategory
    {
        $expense_category->fill($data);

        $expense_category->save();

        return $expense_category;
    }

    public function update(array $data, ExpenseCategory $expense_category): ExpenseCategory
    {
        $expense_category->update($data);

        return $expense_category;
    }

    /**
     * @param int $id
     * @return ExpenseCategory
     */
    public function findCategoryById(int $id): ExpenseCategory
    {
        return $this->findOneOrFail($id);
    }

}
