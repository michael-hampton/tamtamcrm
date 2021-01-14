<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Expense $expense
     * @return mixed
     */
    public function view(User $user, Expense $expense)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expense->user_id === $user->id || $user->hasPermissionTo(
                'expensecontroller.show'
            ) || (!empty($expense->assigned_to) && $expense->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Expense $expense
     * @return mixed
     */
    public function delete(User $user, Expense $expense)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expense->user_id === $user->id || $user->hasPermissionTo(
                'expensecontroller.destroy'
            ) || (!empty($expense->assigned_to) && $expense->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Expense $expense
     * @return mixed
     */
    public function update(User $user, Expense $expense)
    {
        return $user->account_user()->is_admin || $user->account_user(
            )->is_owner || $expense->user_id === $user->id || $user->hasPermissionTo(
                'expensecontroller.update'
            ) || (!empty($expense->assigned_to) && $expense->assigned_to === $user->id);
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
                'expensecontroller.store'
            );
    }
}
