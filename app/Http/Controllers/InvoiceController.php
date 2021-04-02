<?php

namespace App\Http\Controllers;

use App\Actions\Plan\ApplyCode;
use App\Components\InvoiceCalculator\LineItem;
use App\Events\Plan\PlanWasInvoiced;
use App\Factory\InvoiceFactory;
use App\Models\Audit;
use App\Models\Customer;
use App\Models\CustomerPlan;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Task;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\TaskRepository;
use App\Requests\Invoice\CreateInvoiceRequest;
use App\Requests\Invoice\CreateSubscriptionInvoiceRequest;
use App\Requests\Invoice\UpdateInvoiceRequest;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use App\Transformations\AuditTransformable;
use App\Transformations\InvoiceTransformable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class InvoiceController
 * @package App\Http\Controllers
 */
class InvoiceController extends BaseController
{
    /**
     * @var InvoiceRepositoryInterface|InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * InvoiceController constructor.
     * @param InvoiceRepositoryInterface $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param CreditRepository $credit_repo
     */
    public function __construct(
        InvoiceRepositoryInterface $invoice_repo,
        QuoteRepository $quote_repo,
        CreditRepository $credit_repo
    ) {
        $this->invoice_repo = $invoice_repo;
        parent::__construct($invoice_repo, $quote_repo, $credit_repo, 'Invoice');
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $invoices =
            (new InvoiceSearch($this->invoice_repo))->filter($request, auth()->user()->account_user()->account);

        return response()->json($invoices);
    }

    public function getInvoicesByStatus(int $status)
    {
        $invoices = $this->invoice_repo->findInvoicesByStatus($status);
        return response()->json($invoices);
    }

    /**
     * @param CreateInvoiceRequest $request
     * @return string
     */
    public function store(CreateInvoiceRequest $request)
    {
        $customer = Customer::find($request->input('customer_id'));
        $invoice = $this->invoice_repo->create(
            $request->all(),
            InvoiceFactory::create(auth()->user()->account_user()->account, auth()->user(), $customer)
        );

        return response()->json((new InvoiceTransformable())->transformInvoice($invoice));
    }

    /**
     * @param int $invoice_id
     * @return mixed
     * @throws Exception
     */
    public function show(Invoice $invoice)
    {
        return response()->json((new InvoiceTransformable())->transformInvoice($invoice));
    }

    /**
     * @param int $task_id
     * @return mixed
     * @throws Exception
     */
    public function getInvoiceLinesForTask(int $task_id)
    {
        $task = (new TaskRepository(new Task))->findTaskById($task_id);
        $invoice = $this->invoice_repo->getInvoiceForTask($task);

        if (!$invoice->count()) {
            return response()->json('empty');
        }

        $arrTest = [
            'lines'   => $invoice->line_items,
            'invoice' => $invoice
        ];

        return response()->json($arrTest);
    }

    /**
     * @param UpdateInvoiceRequest $request
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        if ($invoice->isLocked()) {
            return response()->json(['message' => trans('texts.invoice_is_locked')], 422);
        }

        $invoice = $this->invoice_repo->update($request->all(), $invoice);
        return response()->json((new InvoiceTransformable())->transformInvoice($invoice));
    }

    public function action(Request $request, Invoice $invoice, $action)
    {
        return $this->performAction($request, $invoice, $action);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function archive(Invoice $invoice)
    {
        $invoice->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->deleteInvoice();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $invoice = Invoice::withTrashed()->where('id', '=', $id)->first();
        $invoice->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param CreateSubscriptionInvoiceRequest $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function createSubscriptionInvoice(CreateSubscriptionInvoiceRequest $request)
    {
        $account = auth()->user()->account_user()->account;
        $customer = Customer::find($request->input('customer_id'));

        // get plan and create subscription
        $plan = Plan::find($request->input('plan_id'));
        $customer->newSubscription('main', $plan, $account, $request->input('quantity'));
        $subscription = $customer->activeSubscriptions()->first();

        if (!empty($plan->trial_period)) {
            return response()->json($subscription);
        }

        $due_date = $subscription->due_date->format('Y-m-d');

        $unit_cost = $plan->price;

        $data = $request->input('invoice');
        $data['date'] = now();
        $data['due_date'] = $due_date;
        $data['plan_subscription_id'] = $subscription->id;

        if (!empty($request->input('promocode')) && empty($plan->promocode_applied)) {
            $promocode = (new ApplyCode())->execute($plan, $account, $unit_cost);
            $data['discount_total'] = $promocode['amount'];
            $data['voucher_code'] = $promocode['promocode'];
            $data['is_amount_discount'] = $promocode['is_amount_discount'];
        }

        $line_items[] = (new LineItem())
            ->setProductId($subscription->id)
            ->setQuantity($request->input('quantity'))
            ->setUnitPrice($unit_cost)
            ->setTypeId(Invoice::SUBSCRIPTION_TYPE)
            ->setNotes("Plan charge for " . auth()->user()->account_user()->account->subdomain)
            ->toObject();

        $data['line_items'] = $line_items;

        $invoice_repo = new InvoiceRepository(new Invoice);
        $invoice = $invoice_repo->create(
            $data,
            InvoiceFactory::create(
                $account,
                auth()->user(),
                $customer
            )
        );
        $invoice_repo->markSent($invoice);

        $event_data = [
            'plan_id'         => $plan->id,
            'subscription_id' => $subscription->id,
            'invoice_id'      => $invoice->id,
            'voucher_code'    => !empty($data['voucher_code']) ? $data['voucher_code'] : ''
        ];

        event(new PlanWasInvoiced($subscription, $event_data));

        return response()->json($invoice);
    }

    public function audits($model, int $id)
    {
        $class = "App\\Models\\{$model}";

        $invoice = $class::where('id', '=', $id)->first();

        $audits = $invoice->audits;

        $audits = $audits->map(
            function (Audit $audit) {
                return (new AuditTransformable())->transformAudit($audit);
            }
        )->all();

        return response()->json($audits);
    }
}
