<?php


namespace App\Components\Reports;


use App\Models\Account;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Product;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

class LineItemReport
{
    public function build(Request $request, Account $account)
    {
        $invoices = Invoice::where('account_id', $account->id)->get();

        if (!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
            $start = date("Y-m-d", strtotime($request->input('start_date')));
            $end = date("Y-m-d", strtotime($request->input('end_date')));
            $invoices = $invoices->whereBetween('date', [$start, $end]);
        }

        $products = Product::where('account_id', $account->id)->get()->keyBy('id');
        $currencies = Currency::get()->keyBy('id');

        $groups = [];
        $reports = [];
        $currency_report = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->line_items as $line_item) {
                if ($line_item->type_id !== Invoice::PRODUCT_TYPE) {
                    continue;
                }

                $reports[] = [
                    'invoice'  => $invoice->number,
                    'product'  => $products[$line_item->product_id]->name,
                    'quantity' => $line_item->quantity,
                    'price'    => $line_item->unit_price,
                    'total'    => $line_item->unit_price * $line_item->quantity
                ];

                if (!isset($currency_report[$currencies[$invoice->currency_id]->id])) {
                    $currency_report[$currencies[$invoice->currency_id]->id] = [
                        'name'  => $currencies[$invoice->currency_id]->name,
                        'total' => 0,
                        'count' => 0
                    ];
                }

                $currency_report[$currencies[$invoice->currency_id]->id]['total'] += $line_item->unit_price * $line_item->quantity;
                $currency_report[$currencies[$invoice->currency_id]->id]['count']++;
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
                                'invoice'  => $group_by === 'invoice' ? $key : null,
                                'product'  => $group_by === 'product' ? $key : null,
                                // $key is what we grouped by, it'll be constant by each  group of rows
                                'quantity' => $group->sum('quantity'),
                                'price'    => $group->sum('price'),
                                'total'    => $group->sum('total'),
                                'count'    => $group->count(),
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