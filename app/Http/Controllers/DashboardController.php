<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Repositories\DealRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Requests\SearchRequest;
use App\Transformations\DashboardTransformer;
use App\Transformations\TaskTransformable;
use Carbon\Carbon;

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
        $search_request = new SearchRequest();
        $search_request->replace(['column' => 'created_at', 'order' => 'desc']);

        $account = auth()->user()->account_user()->account;

        $test = $account->with(
            [
                'customers' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_customers']);
                }
            ]
        )->with(
            [
                'invoices' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_invoices']);
                }
            ]
        )->with(
            [
                'credits' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_credits']);
                }
            ]
        )->with(
            [
                'payments' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_payments']);
                }
            ]
        )->with(
            [
                'quotes' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_quotes']);
                }
            ]
        )->with(
            [
                'orders' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_orders']);
                }
            ]
        )->with(
            [
                'expenses' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_expenses']);
                }
            ]
        )->with(
            [
                'tasks' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_tasks']);
                }
            ]
        )->with(
            [
                'leads' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user())->cacheFor(
                        now()->addMonthNoOverflow()
                    )->cacheTags(['dashboard_leads']);
                }
            ]
        )->with(
            [
                'deals' => function ($query) {
                    $query->orderBy('created_at', 'desc')->permissions(auth()->user());
                }
            ]
        )->first();

        $data = (new DashboardTransformer())->transformDashboardData($test);

        $date = Carbon::today()->subDays(3);

        $deal_repo = new DealRepository(new Deal);
        $arrSources = $this->taskRepository->getSourceTypeCounts(3, $account->id);
        $arrStatuses = $this->taskRepository->getStatusCounts($account->id);
        $leadsToday = $test->tasks->where('created_at', '>=', $date)->count();
        $customersToday = $test->customers->where('created_at', '>=', $date)->count();
        $newDeals = $test->deals->where('created_at', '>=', $date)->count();
        $totalEarnt = $test->deals->sum('valued_at');

        $data['sources'] = $arrSources->toArray();
        $data['leadCounts'] = $arrStatuses->toArray();
        $data['totalBudget'] = number_format($totalEarnt, 2);
        $data['totalEarnt'] = number_format($totalEarnt, 2);
        $data['leadsToday'] = number_format($leadsToday, 2);
        $data['newDeals'] = number_format($newDeals, 2);
        $data['newCustomers'] = number_format($customersToday, 2);

        /*$arrOutput = [
            'customers'    => (new CustomerRepository(new Customer()))->getAll(
                $search_request,
                $account
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
                $search_request,
                $account
            ),
            'quotes'       => (new QuoteRepository(new Quote()))->getAll(
                $search_request,
                $account
            ),
            'credits'      => (new CreditRepository(new Credit()))->getAll(
                $search_request,
                $account
            ),
            'payments'     => (new PaymentRepository(new Payment()))->getAll(
                $search_request,
                $account
            ),
            'orders'       => (new OrderRepository(new Order()))->getAll(
                $search_request,
                $account
            ),
            'expenses'     => (new ExpenseRepository(new Expense()))->getAll(
                $search_request,
                $account
            ),
            'tasks'        => (new TaskRepository(new Task(), new ProjectRepository(new Project())))->getAll(
                $search_request,
                $account
            )
        ]; */

        return response()->json($data);
    }

}
