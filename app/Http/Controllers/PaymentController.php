<?php

namespace App\Http\Controllers;

use App\Components\Payment\DeletePayment;
use App\Components\Payment\ProcessPayment;
use App\Components\Refund\RefundFactory;
use App\Events\Payment\PaymentWasCreated;
use App\Factory\PaymentFactory;
use App\Jobs\Payment\CreatePayment;
use App\Models\CompanyGateway;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Repositories\CreditRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Requests\Payment\CreatePaymentRequest;
use App\Requests\Payment\RefundPaymentRequest;
use App\Requests\Payment\UpdatePaymentRequest;
use App\Requests\SearchRequest;
use App\Search\PaymentSearch;
use App\Transformations\PaymentTransformable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{

    use PaymentTransformable;

    /**
     * @var PaymentRepositoryInterface
     */
    private $payment_repo;

    /**
     * PaymentController constructor.
     * @param PaymentRepositoryInterface $payment_repo
     */
    public function __construct(PaymentRepositoryInterface $payment_repo)
    {
        $this->payment_repo = $payment_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $payments =
            (new PaymentSearch($this->payment_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($payments);
    }

    /**
     * Store a newly created resource in storage.
     * @param CreatePaymentRequest $request
     * @return mixed
     */
    public function store(CreatePaymentRequest $request)
    {
        $payment = PaymentFactory::create(
            Customer::where('id', $request->customer_id)->first(),
            auth()->user(),
            auth()->user()->account_user()->account
        );

        $payment = (new ProcessPayment())->process($request->all(), $this->payment_repo, $payment);

        if ($request->input('send_email') === true) {
            $payment->service()->sendEmail();
        }

        event(new PaymentWasCreated($payment));

        return response()->json($this->transformPayment($payment));
    }

    public function show(int $id)
    {
        $payment = $this->payment_repo->findPaymentById($id);
        return response()->json($this->transformPayment($payment));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePaymentRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdatePaymentRequest $request, $id)
    {
        $payment = $this->payment_repo->findPaymentById($id);
        $payment = (new ProcessPayment())->process($request->all(), $this->payment_repo, $payment);
        return response()->json($this->transformPayment($payment));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $payment = $this->payment_repo->findPaymentById($id);

        $this->authorize('delete', $payment);

        (new DeletePayment($payment))->execute();

        return response()->json('deleted');
    }

    public function bulk()
    {
        $action = request()->input('action');

        $ids = request()->input('ids');
        $payments = Payment::withTrashed()->find($ids);
        $payments->each(
            function ($payment, $key) use ($action) {
                $this->payment_repo->{$action}($payment);
            }
        );
        return response()->json(Payment::withTrashed()->whereIn('id', $ids));
    }

    /**
     * @param Request $request
     * @param Payment $payment
     * @param $action
     * @return JsonResponse
     */
    public function action(Request $request, Payment $payment, $action)
    {
        if ($action === 'refund') {
            $payment = (new RefundFactory())->createRefund($payment, $request->all(), new CreditRepository(new Credit));
            return response()->json($this->transformPayment($payment));
        }

        if ($action === 'email') {
            $payment->service()->sendEmail();
            return response()->json(['email sent']);
        }

        if ($action === 'archive') {
            return $this->archive($payment->id);
        }
    }

    public function archive(int $id)
    {
        $payment = Payment::withTrashed()->where('id', '=', $id)->first();
        $payment->archive();
        return response()->json([], 200);
    }

    /**
     * @param RefundPaymentRequest $request
     * @return JsonResponse
     */
    public function refund(RefundPaymentRequest $request)
    {
        $payment = $request->payment();

        $payment = (new RefundFactory())->createRefund($payment, $request->all(), new CreditRepository(new Credit));

        return response()->json($this->transformPayment($payment));
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $payment = Payment::withTrashed()->where('id', '=', $id)->first();

        if ($payment->is_deleted === true) {
            return response()->json('Unable to restore deleted payment', 500);
        }

        $payment->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function completePayment(Request $request)
    {
        $payment = CreatePayment::dispatchNow($request->all(), $this->payment_repo);

        return response()->json(['code' => 200, 'payment_id' => $payment->id], 200);
    }

    public function buyNow(string $invoice_number, Request $request)
    {
        $invoice = Invoice::where('number', '=', $invoice_number)->first();
        $company_gateway = CompanyGateway::where('gateway_key', '=', '64bcbdce')->first();

        $return_url = 'http://' . config(
                'taskmanager.app_domain'
            ) . '/pay_now/success?invoice_number=' . $invoice_number;

        return view(
            'payment.buy_now',
            ['invoice' => $invoice, 'company_gateway' => $company_gateway, 'return_url' => $return_url]
        );
    }

    public function buyNowSuccess()
    {
        die('return');
    }
}
