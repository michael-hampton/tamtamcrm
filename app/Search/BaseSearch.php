<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Invoice;
use Carbon\Carbon;

class BaseSearch
{

    protected function filterDates($request)
    {
        $start = date("Y-m-d", strtotime($request->input('start_date')));
        $end = date("Y-m-d", strtotime($request->input('end_date')));
        $this->query->whereBetween('created_at', [$start, $end]);
    }

    protected function orderBy($orderBy, $orderDir)
    {
        $this->query->orderBy($orderBy, $orderDir);
    }

    /**
     * @param Account $account
     * @param string $table
     */
    protected function addAccount(Account $account, $table = '')
    {
        $field = !empty($table) ? $table . '.account_id' : 'account_id';
        $this->query->where($field, '=', $account->id);
    }

    /**
     * Filters the list based on the status
     * archived, active, deleted
     * @param string $table
     * @param string $filter
     * @param string $column
     * @return mixed
     */
    protected function status(string $table, $filter = '', $column = '')
    {
        if ($filter === null || strlen($filter) == 0) {
            return $this->query;
        }

        $statuses = explode(',', $filter);
        $column = $column !== '' ? $column : 'status_id';
        $filtered_statuses = [];

        foreach ($statuses as $status) {
            if (is_numeric($status)) {
                $filtered_statuses[] = $status;
                continue;
            }

            $this->doStatusFilter($status, $table);
        }

        if (!empty($filtered_statuses)) {
            $this->query->whereIn(
                $column,
                $filtered_statuses
            );
        }

        return true;
    }

    private function doStatusFilter($status, $table)
    {
        if ($status === 'invoice_overdue') {
            $this->query->whereIn(
                'status_id',
                [
                    Invoice::STATUS_SENT,
                    Invoice::STATUS_PARTIAL
                ]
            )->where('due_date', '<', Carbon::now())->orWhere('partial_due_date', '<', Carbon::now());
        }

        if ($status === 'unapplied') {
            $this->query->whereRaw("{$table}.applied < {$table}.amount");
        }

        if ($status === 'active') {
            $this->query->whereNull($table . '.deleted_at');
        }

        if ($status === 'archived') {
            $this->query->whereNotNull($table . '.deleted_at')->where($table . '.is_deleted', '=', 0)->withTrashed();
        }

        if ($status === 'deleted') {
            $this->query->where($table . '.is_deleted', '=', 1)->withTrashed();
        }
    }

    protected function getEloquentSqlWithBindings($query)
    {
        return vsprintf(
            str_replace('?', '%s', $query->toSql()),
            collect($query->getBindings())->map(
                function ($binding) {
                    return is_numeric($binding) ? $binding : "'{$binding}'";
                }
            )->toArray()
        );
    }
}
