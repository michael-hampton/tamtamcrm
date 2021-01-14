<?php

namespace App\Http\Controllers;

use App\Factory\OrderFactory;
use App\Models\Customer;
use App\Models\Order;
use App\Repositories\CreditRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuoteRepository;
use App\Requests\Order\CreateOrderRequest;
use App\Requests\Order\UpdateOrderRequest;
use App\Requests\SearchRequest;
use App\Search\OrderSearch;
use App\Transformations\OrderTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    use OrderTransformable;

    /**
     * @var OrderRepository
     */
    private OrderRepository $order_repo;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * OrderController constructor.
     * @param OrderRepository $order_repo
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param CreditRepository $credit_repo
     */
    public function __construct(
        OrderRepository $order_repo,
        InvoiceRepository $invoice_repo,
        QuoteRepository $quote_repo,
        CreditRepository $credit_repo
    ) {
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'Order');
        $this->order_repo = $order_repo;
        $this->invoice_repo = $invoice_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $invoices =
            (new OrderSearch($this->order_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($invoices);
    }

    /**
     * @param int $id
     * @param UpdateOrderRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(int $id, UpdateOrderRequest $request)
    {
        $order = $this->order_repo->findOrderById($id);
        $this->order_repo->updateOrder($request->all(), $order);
        return response()->json($this->transformOrder($order));
    }

    /**
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrderRequest $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        $order = $this->order_repo->createOrder(
            $request->all(),
            OrderFactory::create(auth()->user()->account_user()->account, auth()->user(), $customer)
        );

        return response()->json($this->transformOrder($order));
    }

    public function show(int $id)
    {
        $order = $this->order_repo->findOrderById($invoice_id);
        return response()->json($this->transformOrder($order));
    }

    public function getOrderForTask(int $task_id)
    {
        $order = Order::whereTaskId($task_id)->first();
        return response()->json($this->transformOrder($order));
    }

    public function action(Request $request, Order $order, $action)
    {
        return $this->performAction($request, $order, $action);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function archive(int $id)
    {
        $order = $this->order_repo->findOrderById($id);
        $order->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $order = $this->order_repo->findOrderById($id);

        $this->authorize('delete', $order);

        $order->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function restore(int $id)
    {
        $order = $this->order_repo->findOrderById($id);
        $order->restoreEntity();
        return response()->json([], 200);
    }
}
