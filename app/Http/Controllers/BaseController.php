<?php


namespace App\Http\Controllers;


use App\Events\Misc\InvitationWasViewed;
use App\Factory\CloneCreditFactory;
use App\Factory\CloneCreditToQuoteFactory;
use App\Factory\CloneInvoiceFactory;
use App\Factory\CloneInvoiceToQuoteFactory;
use App\Factory\CloneOrderToInvoiceFactory;
use App\Factory\CloneOrderToQuoteFactory;
use App\Factory\CloneQuoteFactory;
use App\Factory\InvoiceToRecurringInvoiceFactory;
use App\Factory\QuoteToRecurringQuoteFactory;
use App\Factory\RecurringInvoiceToInvoiceFactory;
use App\Factory\RecurringQuoteToQuoteFactory;
use App\Jobs\Pdf\Download;
use App\Models\AccountUser;
use App\Models\Country;
use App\Models\Credit;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Language;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Models\Quote;
use App\Models\RecurringInvoice;
use App\Models\RecurringQuote;
use App\Models\TaxRate;
use App\Models\User;
use App\Repositories\CreditRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\RecurringInvoiceRepository;
use App\Repositories\RecurringQuoteRepository;
use App\Transformations\CreditTransformable;
use App\Transformations\InvoiceTransformable;
use App\Transformations\OrderTransformable;
use App\Transformations\PurchaseOrderTransformable;
use App\Transformations\QuoteTransformable;
use App\Transformations\RecurringInvoiceTransformable;
use App\Transformations\RecurringQuoteTransformable;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use ReflectionException;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    use CreditTransformable;
    use RecurringQuoteTransformable;
    use RecurringInvoiceTransformable;
    use OrderTransformable;
    use PurchaseOrderTransformable;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quote_repo;

    /**
     * @var CreditRepository
     */
    private CreditRepository $credit_repo;

    /**
     * @var string
     */
    private string $entity_string;

    /**
     * BaseController constructor.
     * @param InvoiceRepository $invoice_repo
     * @param QuoteRepository $quote_repo
     * @param CreditRepository $credit_repo
     * @param string $entity_string
     */
    public function __construct(
        InvoiceRepository $invoice_repo,
        QuoteRepository $quote_repo,
        CreditRepository $credit_repo,
        string $entity_string
    ) {
        $this->invoice_repo = $invoice_repo;
        $this->quote_repo = $quote_repo;
        $this->credit_repo = $credit_repo;
        $this->entity_string = $entity_string;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function bulk(Request $request)
    {
        $action = $request->action;

        $ids = $request->ids;

        $class = "App\Models\\{$this->entity_string}";

        $entities = $class::withTrashed()->whereIn('id', $ids)->get();

        if (!$entities) {
            return response()->json(['message' => "No {$this->entity_string} Found"]);
        }

        if ($action == 'download' && $entities->count() >= 1) {
            Download::dispatch($entities, $entities->first()->account, auth()->user()->email);

            return response()->json(['message' => 'The email was sent successfully!'], 200);
        }

        $responses = [];

        foreach ($entities as $entity) {
            $response = $this->performAction($request, $entity, $action, true);

            if ($response === false) {
                $responses[] = "FAILED";
                continue;
            }

            $responses[] = $response;
        }

        return response()->json($responses);
    }

    /**
     * @param Request $request
     * @param $entity
     * @param $action
     * @param bool $bulk
     * @return array|bool|JsonResponse|string
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    protected function performAction(Request $request, $entity, $action, $bulk = false)
    {
        switch ($action) {
            //order
            case 'clone_order_to_invoice':
                $invoice = CloneOrderToInvoiceFactory::create(
                    $entity,
                    auth()->user(),
                    auth()->user()->account_user()->account
                );

                $this->invoice_repo->createInvoice($request->all(), $invoice);
                $response = (new InvoiceTransformable())->transformInvoice($invoice);
                break;
            case 'clone_order_to_quote': // done
                $quote = CloneOrderToQuoteFactory::create(
                    $entity,
                    auth()->user(),
                    auth()->user()->account_user()->account
                );
                $this->quote_repo->createQuote($request->all(), $quote);
                $response = (new QuoteTransformable())->transformQuote($quote);
                break;

            case 'hold_order':
                $order = $entity->service()->holdOrder();

                if (!$order) {
                    $response = false;
                    $message = 'Order is already hold';
                } else {
                    $response = $this->transformOrder($order);
                }

                break;

            case 'dispatch_note':
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($entity->service()->generateDispatchNote(null));
                $response = ['data' => base64_encode($content)];
                break;
            case 'reverse_status':
                $order = $entity->service()->reverseStatus();

                if (!$order) {
                    $response = false;
                    $message = 'Order is not on hold';
                } else {
                    $response = $this->transformEntity($order);
                }

                break;
            case 'dispatch': // done
                if (!in_array(
                    $entity->status_id,
                    [
                        Order::STATUS_BACKORDERED,
                        Order::STATUS_DRAFT,
                        Order::STATUS_SENT,
                        Order::STATUS_COMPLETE
                    ]
                )) {
                    $message = 'Unable to approve this order as it has expired.';
                    $response = false;
                } else {
                    $response = $entity->service()->dispatch($this->invoice_repo, (new OrderRepository(new Order)));
                    $response = $this->transformEntity($response);
                }

                break;

            //quote
            case 'clone_quote_to_invoice': // done
                $invoice = $entity->service()->convertQuoteToInvoice($this->invoice_repo);

                if (!$invoice) {
                    $response = false;
                } else {
                    $response = (new InvoiceTransformable())->transformInvoice($invoice);
                }

                break;
            case 'clone_to_order':
                $order = $entity->service()->convertQuoteToOrder((new OrderRepository(new Order())));

                if (!$order) {
                    $response = false;
                } else {
                    $response = $this->transformOrder($order);
                }
                break;
            case 'clone_to_quote': // done
                $quote = CloneQuoteFactory::create($entity, auth()->user());
                $this->quote_repo->createQuote($request->all(), $quote);
                $response = (new QuoteTransformable())->transformQuote($quote);
                break;
            case 'mark_sent': //done
                $entity = $this->invoice_repo->markSent($entity);

                if (!$entity) {
                    $response = false;
                } else {
                    $response = $this->transformEntity($entity);
                }

                break;
            case 'clone_to_credit': // done
                $credit = CloneCreditFactory::create($entity, auth()->user());
                $this->credit_repo->createCreditNote($request->all(), $credit);
                $response = $this->transformCredit($credit);
                break;
            case 'clone_credit_to_quote': //done
                $quote = CloneCreditToQuoteFactory::create($entity, auth()->user());
                (new QuoteRepository(new Quote))->createQuote($request->all(), $quote);
                $response = (new QuoteTransformable())->transformQuote($quote);
                break;

            case 'approve': //done
                $quote = $this->entity_string === 'PurchaseOrder' ? $entity->service()->approve(
                    new PurchaseOrderRepository($entity)
                ) : $entity->service()->approve($this->invoice_repo, $this->quote_repo);

                if (!$quote) {
                    $message = 'Unable to approve this quote as it has expired.';
                    $response = false;
                } else {
                    $quote->save();

                    $response = $this->transformEntity($quote);
                }

                break;

            case 'reject': //done
                $quote = $this->entity_string === 'PurchaseOrder' ? $entity->service()->reject(
                    new PurchaseOrderRepository($entity),
                    $request->all()
                ) : $entity->service()->reject($this->invoice_repo, $this->quote_repo, $request->all());

                if (!$quote) {
                    $message = 'Unable to reject this quote as it has expired.';
                    $response = false;
                } else {
                    $quote->save();

                    $response = $this->transformEntity($quote);
                }

                break;
            case 'change_requested': //done
                $quote = $this->entity_string === 'PurchaseOrder' ? $entity->service()->requestChange(
                    new PurchaseOrderRepository($entity),
                    $request->all()
                ) : $entity->service()->requestChange($this->invoice_repo, $this->quote_repo, $request->all());

                if (!$quote) {
                    $message = 'Unable to update the quote as it has expired.';
                    $response = false;
                } else {
                    $quote->save();

                    $response = $this->transformEntity($quote);
                }

                break;

            case 'download': //done
                $disk = config('filesystems.default');
                $content = Storage::disk($disk)->get($entity->service()->generatePdf(null));
                $response = ['data' => base64_encode($content)];
                break;
            case 'archive': //done
                $entity->archive();
                $response = $this->transformEntity($entity);
                break;
            case 'delete': //done
                $entity->deleteEntity();
                $response = $this->transformEntity($entity);
                break;
            case 'email': //done
                $template = strtolower($this->entity_string);
                $subject = $entity->customer->getSetting('email_subject_' . $template);
                $body = $entity->customer->getSetting('email_template_' . $template);
                $entity->service()->sendEmail(null, $subject, $body);
                $response = $this->transformEntity($entity);
                break;
            case 'clone_to_invoice': // done
                $entity->fill($request->all());

                $invoice = CloneInvoiceFactory::create(
                    $entity,
                    auth()->user(),
                    auth()->user()->account_user()->account
                );
                $this->invoice_repo->createInvoice([], $invoice);
                $response = (new InvoiceTransformable())->transformInvoice($invoice);
                break;
            case 'clone_invoice_to_quote': // done
                $quote = CloneInvoiceToQuoteFactory::create($entity, auth()->user());
                (new QuoteRepository(new Quote))->createQuote($request->all(), $quote);
                $response = (new QuoteTransformable())->transformQuote($quote);
                break;
            case 'create_payment': // done
                $invoice = $entity->service()->createPayment($this->invoice_repo, new PaymentRepository(new Payment));

                if (!$invoice) {
                    $response = false;
                    $message = 'Unable to mark invoice as paid';
                } else {
                    $response = (new InvoiceTransformable())->transformInvoice($invoice);
                }

                break;
            case 'clone_recurring_to_quote':
                $quote = RecurringQuoteToQuoteFactory::create($entity, $entity->customer);
                (new QuoteRepository(new Quote()))->createQuote([], $quote);
                $response = (new QuoteTransformable())->transformQuote($quote);
                break;

            case 'clone_invoice_to_recurring':
                $recurring_invoice = (new RecurringInvoiceRepository(new RecurringInvoice()))->save(
                    $request->all(),
                    InvoiceToRecurringInvoiceFactory::create(
                        $entity
                    )
                );

                $response = $this->transformEntity($recurring_invoice);
                break;

            case 'clone_quote_to_recurring':
                $recurring_quote = (new RecurringQuoteRepository(new RecurringQuote()))->save(
                    $request->all(),
                    QuoteToRecurringQuoteFactory::create(
                        $entity
                    )
                );
                $response = $this->transformEntity($recurring_quote);
                break;

            case 'clone_recurring_to_invoice':
                $invoice = RecurringInvoiceToInvoiceFactory::create($entity, $entity->customer);
                (new InvoiceRepository(new Invoice))->createInvoice([], $invoice);
                $response = (new InvoiceTransformable())->transformInvoice($invoice);
                break;
            case 'reverse': // done
                $invoice = $entity->service()->reverseInvoicePayment(
                    new CreditRepository(new Credit),
                    new PaymentRepository(new Payment)
                );

                if (!$invoice) {
                    $response = false;
                    $message = 'Unable to reverse invoice payment';
                } else {
                    $response = (new InvoiceTransformable())->transformInvoice($invoice);
                }

                break;

            case 'cancel': //done
                $method = "cancel{$this->entity_string}";
                $entity = $entity->service()->{$method}();
                $response = $this->transformEntity($entity);

                break;
            case 'start_recurring':
                $todays_date = Carbon::now()->addHours(1);

                if (empty($entity->date_to_send) || $entity->date_to_send->lte($todays_date)) {
                    return response()->json('The next send date must be in the future', 422);
                }

                $entity->status_id = RecurringInvoice::STATUS_ACTIVE;
                $entity->save();
                $response = $this->transformEntity($entity->fresh());

                break;

            case 'stop_recurring':
                $entity->status_id = RecurringInvoice::STATUS_STOPPED;
                $entity->save();
                $response = $this->transformEntity($entity->fresh());
                break;
            default:
                $response = false;
                $message = "The requested action `{$action}` is not available.";
                break;
        }

        if ($bulk === true) {
            return $response;
        }

        if ($response !== false) {
            return response()->json($response);
        }

        if (isset($message)) {
            return response()->json($message);
        }

        return response()->json('The request action failed to complete');
    }

    /**
     * @param $entity
     * @return array
     * @throws ReflectionException
     */
    private function transformEntity($entity)
    {
        $entity_class = (new ReflectionClass($entity))->getShortName();

        switch ($entity_class) {
            case 'Invoice':
                return (new InvoiceTransformable())->transformInvoice($entity);

            case 'RecurringInvoice':
                return $this->transformRecurringInvoice($entity);

            case 'RecurringQuote':
                return $this->transformRecurringQuote($entity);

            case 'Credit':
                return $this->transformCredit($entity);

            case 'Quote':
                return (new QuoteTransformable())->transformQuote($entity);

            case 'Order':
                return $this->transformOrder($entity);
            case 'PurchaseOrder':
                return $this->transformPurchaseOrder($entity);
            default:
                return $entity;
        }
    }

    public function downloadPdf()
    {
        $ids = request()->input('ids');

        $class = "App\Models\\{$this->entity_string}";

        $entities = $class::withTrashed()->whereIn('id', $ids)->get();

        if (!$entities) {
            return response()->json(['message' => "No {$this->entity_string} Found"]);
        }

        $disk = config('filesystems.default');
        $pdfs = [];

        foreach ($entities as $entity) {
            $content = Storage::disk($disk)->get($entity->service()->generatePdf(null));
            $pdfs[$entity->number] = base64_encode($content);
        }

        return response()->json(['data' => $pdfs]);
    }

    /**
     * @param $invitation_key
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function markViewed($invitation_key)
    {
        $invitation = $this->invoice_repo->getInvitation(['key' => $invitation_key], $this->entity_string);

        $contact = $invitation->contact;
        $entity = $invitation->inviteable;

        $disk = config('filesystems.default');
        $content = Storage::disk($disk)->get($entity->service()->generatePdf($contact));

        if (request()->has('markRead') && request()->boolean('markRead')) {
            $invitation->markViewed();
            event(new InvitationWasViewed(strtolower($this->entity_string), $invitation));
        }

        return response()->json(['data' => base64_encode($content)]);
    }

    protected function getIncludes()
    {
        $user = auth()->user();

        $default_account = $user->accounts->first()->domains->default_company;
        //$user->setAccount($default_account);

        $accounts = AccountUser::whereUserId($user->id)->with('account')->get();

        $custom_fields = !empty(auth()->user()->account_user()->account) ? auth()->user()->account_user(
        )->account->custom_fields : [];

        return [
            'account_id'         => $default_account->id,
            'custom_fields'      => $custom_fields,
            'id'                 => $user->id,
            'auth_token'         => $user->auth_token,
            'name'               => $user->name,
            'email'              => $user->email,
            'accounts'           => $accounts,
            'number_of_accounts' => $user->accounts->count(),
            'currencies'         => Currency::all(),
            'languages'          => Language::all(),
            'countries'          => Country::all(),
            'payment_types'      => PaymentMethod::all(),
            'gateways'           => PaymentGateway::all(),
            'tax_rates'          => TaxRate::all(),
            'users'              => User::where('is_active', '=', 1)->get(
                ['first_name', 'last_name', 'phone_number', 'id']
            )
        ];
    }
}
