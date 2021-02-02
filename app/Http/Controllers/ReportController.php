<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Task;
use App\Repositories\CreditRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DealRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\TaskRepository;
use App\Search\CreditSearch;
use App\Search\CustomerSearch;
use App\Search\DealSearch;
use App\Search\ExpenseSearch;
use App\Search\InvoiceSearch;
use App\Search\LeadSearch;
use App\Search\OrderSearch;
use App\Search\PaymentSearch;
use App\Search\PurchaseOrderSearch;
use App\Search\QuoteSearch;
use App\Search\TaskSearch;
use App\Transformations\TaskTransformable;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    use TaskTransformable;

    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * DashboardController constructor.
     *
     * TaskRepositoryInterface $taskRepository
     * @param TaskRepositoryInterface $taskRepository
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        TaskRepositoryInterface $taskRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request)
    {
        $currency_report = [];

        switch ($request->input('report_type')) {
            case 'lead':
                $report = (new LeadSearch(new LeadRepository(new Lead())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'deal':
                $report = (new DealSearch(new DealRepository(new Deal())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'invoice':
                $report = (new InvoiceSearch(new InvoiceRepository(new Invoice())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new InvoiceSearch(new InvoiceRepository(new Invoice())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'purchase_order':
                $report = (new PurchaseOrderSearch(new PurchaseOrderRepository(new PurchaseOrder())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new PurchaseOrderSearch(
                    new PurchaseOrderRepository(new PurchaseOrder())
                ))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'credit':
                $report = (new CreditSearch(new CreditRepository(new Credit())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new CreditSearch(new CreditRepository(new Credit())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'quote':
                $report = (new QuoteSearch(new QuoteRepository(new Quote())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new QuoteSearch(new QuoteRepository(new Quote())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'order':
                $report = (new OrderSearch(new OrderRepository(new Order())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new OrderSearch(new OrderRepository(new Order())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'task':
                $report = (new TaskSearch(
                    new TaskRepository(new Task(), new ProjectRepository(new Project()))
                ))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'customer':
                $report = (new CustomerSearch(new CustomerRepository(new Customer())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new CustomerSearch(new CustomerRepository(new Customer())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'expense':
                $report = (new ExpenseSearch(new ExpenseRepository(new Expense())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new ExpenseSearch(new ExpenseRepository(new Expense())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;

            case 'payment':
                $report = (new PaymentSearch(new PaymentRepository(new Payment())))->buildReport(
                    $request,
                    auth()->user()->account_user()->account
                );

                $currency_report = (new PaymentSearch(new PaymentRepository(new Payment())))->buildCurrencyReport(
                    $request,
                    auth()->user()->account_user()->account
                );
                break;
            case 'line_item':
                $line_item_report = $this->lineItemReport($request);
                $report = $line_item_report['report'];
                $currency_report = $line_item_report['currency_report'];
                break;
        }

        return response()->json(['report' => $report, 'currency_report' => $currency_report]);
    }

    private function lineItemReport(Request $request)
    {
        $invoices = Invoice::where('account_id', auth()->user()->account_user()->account_id)->get();
       
        if(!empty($request->input('start_date')) && ! empty($request->input('end_date'))) {
             $start = date("Y-m-d", strtotime($request->input('start_date')));
            $end = date("Y-m-d", strtotime($request->input('end_date')));
            $invoices = $invoices->whereBetween('date', [$start, $end]);
        }

        $products = Product::where('account_id', auth()->user()->account_user()->account_id)->get()->keyBy('id');
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

    private function taxRateReport(Request $request)
    {
        $invoices = Invoice::where('account_id', auth()->user()->account_user()->account_id)->get();
        $credits = Credit::where('account_id', auth()->user()->account_user()->account_id)->get();
        $tax_rates = TaxRate::where('account_id', auth()->user()->account_user()->account_id)->get()->keyBy('id');
        $currencies = Currency::get()->keyBy('id');

        if(!empty($request->input('start_date')) && ! empty($request->input('end_date'))) {
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
                    'customer' => $customer->name,
                    'invoice'  => $invoice->number,
                    'date'    => $invoice->date,
                    'total'   => $invoice->total,
                    'tax_name'  => $name,
                    'tax_rate'  => $rate,
                    'tax_amount' => $tax['amount'] ?? 0.0
                    'tax_paid' => $tax['paid'] ?? 0.0,
                    'amount_paid' => $amount_paid,
                    'currency' => $currencies[$customer->currency_id]->name
                ];
           
                if (!isset($currency_report[$currencies[$customer->currency_id]->id])) {
                    $currency_report[$currencies[$customer->currency_id]->id] = [
                        'name'  => $currencies[$customer->currency_id]->name,
                        'total' => 0,
                        'tax_amount' => 0,
                        'tax_paid' => 0,
                        'count' => 0
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
                    'customer' => $customer->name,
                    'invoice'  => $credit->number,
                    'date'    => $credit->date,
                    'total'   => $credit->total,
                    'tax_name'  => $name,
                    'tax_rate'  => $rate,
                    'tax_amount' => $tax['amount'] ?? 0.0
                    'tax_paid' => $tax['paid'] ?? 0.0,
                    'amount_paid' => $amount_paid,
                    'currency' => $currencies[$customer->currency_id]->name
                ];
           
                if (!isset($currency_report[$currencies[$customer->currency_id]->id])) {
                    $currency_report[$currencies[$customer->currency_id]->id] = [
                        'name'  => $currencies[$customer->currency_id]->name,
                        'total' => 0,
                        'tax_amount' => 0,
                        'tax_paid' => 0,
                        'count' => 0
                    ];
                }

                $currency_report[$currencies[$customer->currency_id]->id]['total'] += $credit->total;
                $currency_report[$currencies[$customer->currency_id]->id]['tax_amount'] += $tax['amount'];
                $currency_report[$currencies[$customer->currency_id]->id]['tax_paid'] += $tax['paid'];
                $currency_report[$currencies[$customer->currency_id]->id]['count']++;
            }
        }

        if (!empty($request->input('group_by'))) {
            $group_by = $request->input('group_by');
            $groups = collect($reports)->groupBy($group_by);

            $grouped_report = $groups->mapWithKeys(
                function ($group, $key) use ($group_by) {
                    return [
                        $key =>
                            [
                                'tax_name'  => $group_by === 'tax_name' ? $key : null,
                                'invoice'  => $group_by === 'invoice' ? $key : null,
                                // $key is what we grouped by, it'll be constant by each  group of rows
                                'tax_amount' => $group->sum('tax_amount'),
                                'tax_paid' => $group->sum('tax_paid'),
                                'amount_paid' => $group->sum('amount_paid'),
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
