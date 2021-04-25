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

    public function scopeByCustomer($query, int $customer_id)
    {
        return $query->where('customer_id', $customer_id);
    }

    public function scopeByCategory($query, int $category_id)
    {
        return $query->where('category_id', $category_id);
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
}
