<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Reminders;
use App\Services\Email\DispatchEmail;
use App\Models\CustomerContact;
use App\Models\Invitation;
use App\Repositories\Base\BaseRepository;
use App\Repositories\EmailRepository;
use App\Requests\Email\SendEmailRequest;
use App\Transformations\CaseTransformable;
use App\Transformations\CreditTransformable;
use App\Transformations\DealTransformable;
use App\Transformations\EmailTemplateTransformable;
use App\Transformations\InvoiceTransformable;
use App\Transformations\LeadTransformable;
use App\Transformations\OrderTransformable;
use App\Transformations\PurchaseOrderTransformable;
use App\Transformations\QuoteTransformable;
use App\Transformations\TaskTransformable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionException;

class EmailController extends Controller
{
    use CreditTransformable;
    use OrderTransformable;
    use LeadTransformable;
    use DealTransformable;
    use TaskTransformable;
    use CaseTransformable;
    use PurchaseOrderTransformable;
    use EmailTemplateTransformable;

    private $email_repo;

    public function __construct(EmailRepository $email_repo)
    {
        $this->email_repo = $email_repo;
    }

    public function index()
    {
        $account_id = auth()->user()->account_user()->account->id;

        $list = EmailTemplate::where('account_id', $account_id)->get();
        $reminders = $list->map(
            function (EmailTemplate $email_template) {
                return $this->transformTemplate($email_template);
            }
        )->all();

        return response()->json(collect($reminders)->keyBy('template'));
    }

    public function store(Request $request)
    {
        $templates = array_values($request->input('templates'));

        EmailTemplate::upsert($templates, ['template', 'account_id'], ['subject', 'message']);

        return response()->json('success');
    }

    /**
     * @param SendEmailRequest $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function send(SendEmailRequest $request)
    {
        $to = $request->input('to');
        $entity = ucfirst($request->input('entity'));
        $entity = "App\Models\\$entity";
        $entity_obj = $entity::where('id', '=', $request->input('entity_id'))->withTrashed()->first();

        $contact = null;

        if (!empty($to)) {
            $contact = CustomerContact::where('id', '=', $to)->first();
        } elseif (in_array($entity, ['App\Models\Deal', 'App\Models\Task', 'App\Models\Cases'])) {
            $contact = $entity_obj->customer->primary_contact()->first();
        } elseif ($entity === 'App\Models\Lead') {
            $contact = $entity_obj;
        } else {
            $contact = $entity_obj->invitations->first()->contact;
        }

        (new DispatchEmail($entity_obj))->execute($contact, $request->subject, $request->body);

        if (!in_array(
                $entity,
                ['App\Models\Lead', 'App\Models\Deal', 'App\Models\Task', 'App\Models\Cases']
            ) && $request->mark_sent === true) {
            (new BaseRepository($entity_obj))->markSent($entity_obj);
        }

        $transformed_obj = $this->transformObject($entity_obj);

        if (!$transformed_obj) {
            return response()->json(['message' => 'Unable to transform entity'], 404);
        }

        return response()->json($transformed_obj);
    }

    private function transformObject($entity_object)
    {
        $entity_class = (new ReflectionClass($entity_object))->getShortName();

        switch ($entity_class) {
            case 'Lead':
                return $this->transformLead($entity_object);
            case 'Deal':
                return $this->transformDeal($entity_object);
            case 'Cases':
                return $this->transform($entity_object);
            case 'Task':
                return $this->transformTask($entity_object);
            case 'Credit':
                return $this->transformCredit($entity_object);
            case 'Order':
                return $this->transformOrder($entity_object);
            case 'Quote':
                return (new QuoteTransformable())->transformQuote($entity_object);
            case 'Invoice':
                return (new InvoiceTransformable())->transformInvoice($entity_object);
            case 'PurchaseOrder':
                return $this->transformPurchaseOrder($entity_object);
        }

        return false;
    }

    public function postmark(Request $request)
    {
        $invitation = Invitation::where('email_id', '=', $request->input('MessageID'))->first();

        if (empty($invitation)) {
            return response()->json('Could not find message');
        }

        $status = '';
        $response = $request->input('RecordType');

        if (empty($response)) {
            return response()->json(['message' => 'Unknown status'], 403);
        }

        switch ($response) {
            case 'Delivery':
                $status = 'delivered';
                break;
            case 'Bounce':
                $status = 'bounced';
                break;
            case 'SpamComplaint':
                $status = 'spam';
                break;
        }

        if (!empty($status)) {
            $invitation->update(['email_send_status' => $status]);
            return response()->json(['message' => $status], 200);
        }

        return response()->json(['message' => 'Unknown status'], 403);
    }
}
