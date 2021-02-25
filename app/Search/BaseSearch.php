<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class BaseSearch
{
    protected array $field_mapping = [
        'private_notes'      => '$table.private_notes',
        'public_notes'       => '$table.public_notes',
        'industry'           => 'industries.name',
        'custom1'            => '$table.custom_value1',
        'custom2'            => '$table.custom_value2',
        'custom3'            => '$table.custom_value3',
        'custom4'            => '$table.custom_value4',
        'address_1'          => 'billing.address_1',
        'address_2'          => 'billing.address_2',
        'city'               => 'billing.city',
        'state'              => 'billing.state_code',
        'zip'                => 'billing.zip',
        'country'            => 'billing_country.name',
        'shipping_address_1' => 'shipping.address_1',
        'shipping_address_2' => 'shipping.address_2',
        'shipping_city'      => 'shipping.city',
        'shipping_town'      => 'shipping.state_code',
        'shipping_zip'       => 'shipping.zip',
        'shipping_country'   => 'shipping_country.name',
        'language'           => 'languages.name',
        'customer'           => 'customers.name',
        'currency'           => 'currencies.name',
        'balance'            => '$table.balance',
        'customer_balance'   => 'customers.balance'
    ];
    /**
     * @var array
     */
    private array $statuses = [];

    /**
     * @param $permission
     * @param string $table
     * @return bool
     */
    protected function checkPermissions($permission, $table = '')
    {
        if (empty(auth()->user())) {
            return true;
        }

        $user = auth()->user();

        if ($user->account_user()->is_admin || $user->account_user()->is_owner || $user->hasPermissionTo($permission)) {
            return true;
        }

        $table = !empty($table) ? $table . '.' : '';

        $this->query->where(
            function ($query) use ($table) {
                $query->where($table . 'user_id', auth()->user()->id)
                      ->orWhere($table . 'assigned_to', auth()->user()->id);
            }
        );


        return true;
    }

    protected function filterDates($request, $column = '', $field = '')
    {
        $column = $column !== '' ? $column . '.' : '';
        $field = $field !== '' ? $column . $field : $column . 'created_at';

        $start = date("Y-m-d", strtotime($request->input('start_date')));
        $end = date("Y-m-d", strtotime($request->input('end_date')));
        $this->query->whereBetween($field, [$start, $end]);
    }

    protected function filterByDate($params, $column = '')
    {
        $params = explode('|', $params);
        $field = !empty($column) ? $column . '.' . $params[0] : $params[0];

        switch ($params[1]) {
            case 'last_month':
                $this->query->whereDate($field, '>', Carbon::now()->subMonth());
                break;

            case 'last_year':
                $this->query->whereDate($field, '>', Carbon::now()->subYear());
                break;

            default:
                $this->query->whereDate($field, '>', Carbon::now()->subDays($params[1]));
                break;
        }
    }

    protected function addMonthYearToSelect($table, $field)
    {
        $column = $table . '.' . $field;
        $this->query->addSelect(
            DB::raw(
                "CONCAT (MONTHNAME(" . $column . "), ' ', YEAR(" . $column . ")) AS " . $field
            )
        );
    }

    protected function addGroupBy($table, $group_by, $frequency)
    {
        $column = $table . '.' . $group_by;

        if (empty($frequency)) {
            $this->query->groupBy($column);
            return true;
        }

        switch ($frequency) {
            case 'year':
                $this->query->groupBy(DB::raw("YEAR(" . $column . ")"));
                break;

            case 'month':
                $this->query->groupBy(DB::raw("MONTH(" . $column . ")"));
                break;

            case 'day':
                $this->query->groupBy($column);
                break;

            default:
                $this->query->groupBy($column);
                break;
        }
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

    protected function getStatus($model, int $status)
    {
        $refl = new ReflectionClass($model);
        $consts = $refl->getConstants();

        if (empty($this->statuses)) {
            $this->statuses = [];

            foreach ($consts as $key => $const) {
                if (strpos($key, 'STATUS') !== false) {
                    $this->statuses[$const] = !empty(trans('texts.' . strtolower($key))) ? trans(
                        'texts.' . strtolower($key)
                    ) : $key;
                }
            }
        }

        return !empty($this->statuses[$status]) ? $this->statuses[$status] : null;
    }
}
