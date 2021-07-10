<?php

namespace App\Components\Reports;


use App\Models\Account;
use App\Models\Credit;
use App\Models\Currency;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

class TaxReport
{

    public function build(Request $request, Account $account)
    {
        $invoices = Invoice::where('account_id', $account->id)->get();
        $credits = Credit::where('account_id', $account->id)->get();
        $currencies = Currency::get()->keyBy('id');

        if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
            $start = date("Y-m-d", strtotime($request->input('start_date')));
            $end = date("Y-m-d", strtotime($request->input('end_date')));
            $invoices = $invoices->whereBetween('date', [$start, $end]);
            $credits = $credits->whereBetween('date', [$start, $end]);
        }

        $groups = [];
        $reports = [];
        $currency_report = [];

        foreach ($invoices as $invoice) {
            $amount_paid = $invoice->total - $invoice->balance;
            $customer = $invoice->customer;
            $precision = $currencies[$customer->currency_id]->precision;
            $taxes = $invoice->getTaxes($precision);

            foreach ($taxes as $tax) {
                $row = [];
                $name = $tax['name'];
                $rate = $tax['rate'];

                if (empty($rate)) {
                    continue;
                }

                $reports[] = [
                    'customer'    => $customer->name,
                    'number'      => $invoice->number,
                    'date'        => $invoice->date,
                    'total'       => $invoice->total,
                    'tax_name'    => $name,
                    'tax_rate'    => $rate,
                    'tax_amount'  => $tax['amount'] ?? 0.0,
                    'tax_paid'    => $tax['paid'] ?? 0.0,
                    'amount_paid' => $amount_paid,
                    'currency'    => $currencies[$customer->currency_id]->name
                ];

                if (!isset($currency_report[$currencies[$customer->currency_id]->id])) {
                    $currency_report[$currencies[$customer->currency_id]->id] = [
                        'name'       => $currencies[$customer->currency_id]->name,
                        'total'      => 0,
                        'tax_amount' => 0,
                        'tax_paid'   => 0,
                        'count'      => 0
                    ];
                }

                $currency_report[$currencies[$customer->currency_id]->id]['total'] += $invoice->total;
                $currency_report[$currencies[$customer->currency_id]->id]['tax_amount'] += $tax['amount'];
                $currency_report[$currencies[$customer->currency_id]->id]['tax_paid'] += $tax['paid'];
                $currency_report[$currencies[$customer->currency_id]->id]['count']++;
            }
        }

        foreach ($credits as $credit) {
            $amount_paid = $credit->total - $credit->balance;
            $customer = $credit->customer;
            $precision = $currencies[$customer->currency_id]->precision;
            $taxes = $credit->getTaxes($precision);

            foreach ($taxes as $tax) {
                $row = [];
                $name = $tax['name'];
                $rate = $tax['rate'];

                if (empty($rate)) {
                    continue;
                }

                $reports[] = [
                    'customer'    => $customer->name,
                    'number'      => $credit->number,
                    'date'        => $credit->date,
                    'total'       => $credit->total,
                    'tax_name'    => $name,
                    'tax_rate'    => $rate,
                    'tax_amount'  => $tax['amount'] ?? 0.0,
                    'tax_paid'    => $tax['paid'] ?? 0.0,
                    'amount_paid' => $amount_paid,
                    'currency'    => $currencies[$customer->currency_id]->name
                ];

                if (!isset($currency_report[$currencies[$customer->currency_id]->id])) {
                    $currency_report[$currencies[$customer->currency_id]->id] = [
                        'name'       => $currencies[$customer->currency_id]->name,
                        'total'      => 0,
                        'tax_amount' => 0,
                        'tax_paid'   => 0,
                        'count'      => 0
                    ];
                }

                $currency_report[$currencies[$customer->currency_id]->id]['total'] += $credit->total;
                $currency_report[$currencies[$customer->currency_id]->id]['tax_amount'] += $tax['amount'];
                $currency_report[$currencies[$customer->currency_id]->id]['tax_paid'] += $tax['paid'];
                $currency_report[$currencies[$customer->currency_id]->id]['count']++;
            }
        }

        $order_by = $request->input('orderByField');

        if (!empty($order_by)) {
            $collection = collect($reports);
            $reports = $request->input('orderByDirection') === 'asc' ? $collection->sortby($order_by)->toArray(
            ) : $collection->sortByDesc($order_by)->toArray();
        }

        if (!empty($request->input('group_by'))) {
            $group_by = $request->input('group_by');
            $groups = collect($reports)->groupBy($group_by);

            $grouped_report = $groups->mapWithKeys(
                function ($group, $key) use ($group_by) {
                    return [
                        $key =>
                            [
                                'tax_name'    => $group_by === 'tax_name' ? $key : null,
                                'number'      => $group_by === 'number' ? $key : null,
                                // $key is what we grouped by, it'll be constant by each  group of rows
                                'tax_amount'  => $group->sum('tax_amount'),
                                'tax_paid'    => $group->sum('tax_paid'),
                                'amount_paid' => $group->sum('amount_paid'),
                                'count'       => $group->count(),
                            ]
                    ];
                }
            );
        }

        $report = !empty($request->input('group_by')) ? $grouped_report->toArray() : $reports;

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            $report = (new InvoiceRepository(new Invoice()))->paginateArrayResults($report, $request->input('perPage'));
        }

        return [
            'currency_report' => array_values($currency_report),
            'report'          => $report,
        ];
    }
}