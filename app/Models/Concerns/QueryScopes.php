<?php

namespace App\Models\Concerns;


use App\Models\Customer;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait QueryScopes
{
    public function scopeByDate($query, $date_from, $date_to, $column = '', $field='')
    {
        $column = $column !== '' ? $column . '.' : '';
        $field = $field !== '' ? $column . $field : $column . 'created_at';

        $start = date("Y-m-d", strtotime($date_from));
        $end = date("Y-m-d", strtotime($date_to));
        return $query->whereBetween($field, [$start, $end]);
    }

    public function scopeByAssignee($query, int $user_id)
    {
        $query->where('assigned_to', '=', $user_id);
    }

    public function scopeByCompany($query, int $company_id)
    {
        $query->where('company_id', '=', $company_id);
    }

    public function scopeByCustomer($query, int $customer_id)
    {
        return $query->where('customer_id', $customer_id);
    }

    public function scopeByCategory($query, int $category_id)
    {
        return $query->where('category_id', $category_id);
    }

    public function scopeByExpenseCategory($query, int $category_id)
    {
        return $query->where('expense_category_id', $category_id);
    }

    public function scopeByAccount($query, Account $account, $table = '')
    {
        $column = !empty($table) ? $table . '.account_id' : 'account_id';
        return $query->where($column, $account->id);
    }

    public function scopeByProject($query, int $project_id)
    {
        return $query->where('project_id', $project_id);
    }

    public function scopeById($query, int $id)
    {
        return $query->where('id', $id);
    }

    public function scopeUnapplied($query, $table)
    {
        return $this->query->whereRaw("{$table}.applied < {$table}.amount");
    }

    public function scopeActive($query, $table)
    {
        return $query->whereNull($table . '.deleted_at');
    }

    public function scopeArchived($query, $table)
    {
        return $query->whereNotNull($table . '.deleted_at')->where($table . '.hide', '=', 0)->withTrashed();
    }

    public function scopeDeleted($query, $table)
    {
        return $query->where($table . '.hide', '=', 1)->withTrashed();
    }
}
