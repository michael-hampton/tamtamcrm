<?php


namespace App\Components\Reports;


use App\Models\Account;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeReport
{

    public function build(Request $request, Account $account)
    {
        $expense_query = DB::table('expenses');

        $currency_report = $this->buildCurrencyReport($request, $account);

        $group_by = $request->input('group_by');

        if (!empty($group_by)) {
            $expense_query->select(
                DB::raw(
                    'count(*) as count, customers.name AS customer, companies.name AS company, CONCAT("-", SUM(amount)) as amount'
                )
            )
                          ->groupBy('expenses.' . $group_by);
        } else {
            $expense_query->select(
                'customers.name AS customer',
                'billing.address_1',
                'billing.address_2',
                'shipping.address_1 AS shipping_address1',
                'shipping.address_2 AS shipping_address2',
                'companies.name AS company',
                'companies.town',
                'companies.city',
                'countries.name AS company_country',
                DB::raw("CONCAT('-', expenses.amount) AS amount"),
                'expenses.date',
                //DB::raw("'expense' as type")
            );
        }

        $expense_query->join('customers', 'customers.id', '=', 'expenses.customer_id')
                      ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                      ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                      ->leftJoin('companies', 'expenses.company_id', '=', 'companies.id')
                      ->leftJoin('countries', 'companies.country_id', '=', 'countries.id')
                      ->where('billing.address_type', '=', 1)
                      ->where('shipping.address_type', '=', 2)
                      ->where('expenses.account_id', '=', $account->id);

        $order_by = $request->input('orderByField');
        $order_dir = $request->input('orderByDirection');

        $this->query = DB::table('payments');

        if (!empty($group_by)) {
            $this->query->select(
                DB::raw(
                    'count(*) as count, customers.name AS customer, companies.name AS company, SUM(amount) as amount'
                )
            )
                        ->groupBy('payments.' . $group_by);
        } else {
            $this->query->select(
                'customers.name AS customer',
                'billing.address_1',
                'billing.address_2',
                'shipping.address_1 AS shipping_address1',
                'shipping.address_2 AS shipping_address2',
                'companies.name AS company',
                'companies.town',
                'companies.city',
                'countries.name AS company_country',
                'payments.amount',
                'payments.date',
                //DB::raw("'payment' as type")
            );
        }

        $this->query->join('customers', 'customers.id', '=', 'payments.customer_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('companies', 'payments.company_id', '=', 'companies.id')
                    ->leftJoin('countries', 'companies.country_id', '=', 'countries.id')
                    ->where('billing.address_type', '=', 1)
                    ->where('shipping.address_type', '=', 2)
                    ->where('payments.account_id', '=', $account->id)
                    ->union($expense_query);

        $collection = $this->query->get();

        if (!empty($request->input('date_format'))) {
            //$this->filterByDate($request->input('date_format'));
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($collection, $request);
        }

        if (!empty($order_by)) {
            $collection = $order_dir === 'desc' ? $collection->sortByDesc($order_by) : $collection->sortBy($order_by);
        }

        $report = $collection->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            $report = (new PaymentRepository(new Payment()))->paginateArrayResults($report, $request->input('perPage'));
        }

        return [
            'currency_report' => $currency_report->toArray(),
            'report'          => $report,
        ];
    }

    private function filterDates($collection, $request)
    {
        $start = date("Y-m-d", strtotime($request->input('start_date')));
        $end = date("Y-m-d", strtotime($request->input('end_date')));
        $collection->whereBetween('date', [$start, $end]);
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        $expense_query = DB::table('expenses')
                           ->select(
                               DB::raw('count(*) as count, currencies.name, CONCAT("-", SUM(amount)) as amount')
                           )
                           ->join('currencies', 'currencies.id', '=', 'expenses.currency_id')
                           ->where('currency_id', '<>', 0)
                           ->where('account_id', '=', $account->id)
                           ->groupBy('currency_id');

        return DB::table('payments')
                 ->select(
                     DB::raw('count(*) as count, currencies.name, SUM(amount) as amount')
                 )
                 ->join('currencies', 'currencies.id', '=', 'payments.currency_id')
                 ->where('currency_id', '<>', 0)
                 ->where('account_id', '=', $account->id)
                 ->groupBy('currency_id')
                 ->union($expense_query)
                 ->get();
    }
}