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

class DashboardController extends Controller
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

    public function index()
    {
        $deal_repo = new DealRepository(new Deal);
        $arrSources = $this->taskRepository->getSourceTypeCounts(3, auth()->user()->account_user()->account_id);
        $arrStatuses = $this->taskRepository->getStatusCounts(auth()->user()->account_user()->account_id);
        $leadsToday = $this->taskRepository->getRecentTasks(3, auth()->user()->account_user()->account_id);
        $customersToday = $this->customerRepository->getRecentCustomers(3, auth()->user()->account_user()->account_id);
        $newDeals = $deal_repo->getNewDeals(3, auth()->user()->account_user()->account_id);
        $leads = (new LeadSearch(new LeadRepository(new Lead())))->filter(
            new SearchRequest(),
            auth()->user()->account_user()->account
        );
        $totalEarnt = $deal_repo->getTotalEarnt(auth()->user()->account_user()->account_id);

        $arrOutput = [
            'customers'    => (new CustomerRepository(new Customer()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'sources'      => $arrSources->toArray(),
            'leadCounts'   => $arrStatuses->toArray(),
            'totalBudget'  => number_format($totalEarnt, 2),
            'totalEarnt'   => number_format($totalEarnt, 2),
            'leadsToday'   => number_format($leadsToday, 2),
            'newDeals'     => number_format($newDeals, 2),
            'newCustomers' => number_format($customersToday, 2),
            'deals'        => $leads,
            'invoices'     => (new InvoiceRepository(new Invoice()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'quotes'       => (new QuoteRepository(new Quote()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'credits'      => (new CreditRepository(new Credit()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'payments'     => (new PaymentRepository(new Payment()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'orders'       => (new OrderRepository(new Order()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'expenses'     => (new ExpenseRepository(new Expense()))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            ),
            'tasks'        => (new TaskRepository(new Task(), new ProjectRepository(new Project())))->getAll(
                new SearchRequest(),
                auth()->user()->account_user()->account
            )
        ];

        return response()->json($arrOutput);
    }

}
