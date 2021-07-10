<?php

namespace App\Http\Controllers;

use App\Components\Reports\IncomeReport;
use App\Components\Reports\LineItemReport;
use App\Components\Reports\QuoteLineItemReport;
use App\Components\Reports\TaxReport;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Expense;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Task;
use App\Repositories\CreditRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DealRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\FileRepository;
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
use App\Search\DocumentSearch;
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

            case 'document':
                $report = (new DocumentSearch(new FileRepository(new File())))->buildReport(
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
                $line_item_report = (new LineItemReport())->build($request, auth()->user()->account_user()->account);
                $report = $line_item_report['report'];
                $currency_report = $line_item_report['currency_report'];
                break;
            case 'quote_line_item':
                $line_item_report = (new QuoteLineItemReport())->build($request, auth()->user()->account_user()->account);
                $report = $line_item_report['report'];
                $currency_report = $line_item_report['currency_report'];
                break;
            case 'tax_rate':
                $line_item_report = (new TaxReport())->build($request, auth()->user()->account_user()->account);
                $report = $line_item_report['report'];
                $currency_report = $line_item_report['currency_report'];
                break;
            case 'income':
                $line_item_report = (new IncomeReport())->build($request, auth()->user()->account_user()->account);
                $report = $line_item_report['report'];
                $currency_report = $line_item_report['currency_report'];
                break;
        }

        return response()->json(['report' => $report, 'currency_report' => $currency_report]);
    }

}
