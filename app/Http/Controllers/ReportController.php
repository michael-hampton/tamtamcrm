
<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
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
use App\Repositories\QuoteRepository;
use App\Repositories\TaskRepository;
use App\Requests\SearchRequest;
use App\Search\LeadSearch;
use App\Transformations\TaskTransformable;

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
        
        switch($request->input('report_type')) {
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

                 $currency_report = (new InvoiceSearch(new InvoiceRepository(new Invoice())))->buildCurrencyReport(
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
                 $report = (new LeadSearch(new LeadRepository(new Lead())))->buildReport(
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
        }

        return response()->json(['report' => $report, 'currency_report' => $currency_report]);
    }

}
