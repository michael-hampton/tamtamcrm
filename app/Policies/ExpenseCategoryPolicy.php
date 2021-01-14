<?php

namespace App\Policies;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseCategoryPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ExpenseCategory $expenseCategory
     * @return mixed
     */
    public function view(User $user, ExpenseCategory $expenseCategory)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expenseCategory->user_id === $user->id || $user->hasPermissionTo(
                'expensecategorycontroller.show'
            ) || (!empty($expenseCategory->assigned_to) && $expenseCategory->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ExpenseCategory $expenseCategory
     * @return mixed
     */
    public function delete(User $user, ExpenseCategory $expenseCategory)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expenseCategory->user_id === $user->id || $user->hasPermissionTo(
                'expensecategorycontroller.destroy'
            ) || (!empty($expenseCategory->assigned_to) && $expenseCategory->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ExpenseCategory $expenseCategory
     * @return mixed
     */
    public function update(User $user, ExpenseCategory $expenseCategory)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expenseCategory->user_id === $user->id || $user->hasPermissionTo(
                'expensecategorycontroller.update'
            ) || (!empty($expenseCategory->assigned_to) && $expenseCategory->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->account_user()->is_admin || $user->account_user()->is_owner || $user->hasPermissionTo(
                'expensecategorycontroller.store'
            );
    }
}
