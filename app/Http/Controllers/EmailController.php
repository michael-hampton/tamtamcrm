<?php

namespace App\Http\Controllers;

use App\Models\CustomerContact;
use App\Repositories\Base\BaseRepository;
use App\Repositories\EmailRepository;
use App\Requests\Email\SendEmailRequest;
use App\Traits\MakesInvoiceHtml;
use App\Transformations\CaseTransformable;
use App\Transformations\CreditTransformable;
use App\Transformations\DealTransformable;
use App\Transformations\InvoiceTransformable;
use App\Transformations\LeadTransformable;
use App\Transformations\OrderTransformable;
use App\Transformations\PurchaseOrderTransformable;
use App\Transformations\QuoteTransformable;
use App\Transformations\TaskTransformable;
use Illuminate\Http\JsonResponse;
use ReflectionClass;
use ReflectionException;

class EmailController extends Controller
{
    use MakesInvoiceHtml;
    use CreditTransformable;
    use OrderTransformable;
    use LeadTransformable;
    use DealTransformable;
    use TaskTransformable;
    use CaseTransformable;
    use PurchaseOrderTransformable;

    private $email_repo;

    public function __construct(EmailRepository $email_repo)
    {
        $this->email_repo = $email_repo;
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

        $entity_obj = $entity::find($request->input('entity_id'));
        $contact = null;

        if (!empty($to)) {
            $contact = CustomerContact::where('id', '=', $to)->first();
        } elseif (!in_array($entity, ['App\Models\Lead', 'App\Models\Deal', 'App\Models\Task', 'App\Models\Cases'])) {
            $contact = $entity_obj->invitations->first()->contact;
        }
        $entity_obj->service()->sendEmail($contact, $request->subject, $request->body);

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
}
