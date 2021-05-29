<?php

namespace App\Http\Controllers;

use App\Services\Pdf\GeneratePdf;
use App\Components\Pdf\InvoicePdf;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Design;
use App\Models\Invoice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class PreviewController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Returns a template filled with entity variables
     *
     * @return Response
     *
     */
    public function show()
    {
        $show_html = request()->input('show_html');

        if (!empty(request()->input('entity')) && !empty(request()->input('entity_id'))) {
            $design_object = !empty(request()->input('design')) ? json_decode(
                json_encode(request()->input('design'))
            ) : Design::first()->design;

            if (!is_object($design_object)) {
                return response()->json(['message' => 'Invalid custom design object'], 400);
            }

            $entity = ucfirst(request()->input('entity'));

            $class = "App\Models\\$entity";

            $entity_obj = $class::whereId(request()->input('entity_id'))->first();

            if (!$entity_obj) {
                return $this->blankEntity($show_html);
            }

            $file_path = (new GeneratePdf($entity_obj))->execute(null, true, $show_html);

            if ($show_html === true) {
                return response()->json(['data' => $file_path, 'stylesheet' => public_path('css/pdf.css')]);
            }

            return response()->json(['data' => base64_encode(file_get_contents($file_path))]);
        }

        return $this->blankEntity($show_html);
    }

    private function blankEntity($show_html = false)
    {
        DB::beginTransaction();

        $customer = Customer::factory()->create(
            [
                'user_id'    => auth()->user()->id,
                'account_id' => auth()->user()->account_user()->account_id,
            ]
        );

        $contact = CustomerContact::factory()->create(
            [
                'user_id'                    => auth()->user()->id,
                'account_id'                 => auth()->user()->account_user()->account_id,
                'customer_id'                => $customer->id,
                'is_primary'                 => 1,
                'email_notification_enabled' => true,
            ]
        );

        $address = Address::factory()->create(
            [
                'customer_id'  => $customer->id,
                'address_type' => 1,
            ]
        );

        $invoice = Invoice::factory()->create(
            [
                'user_id'     => auth()->user()->id,
                'account_id'  => auth()->user()->account_user()->account_id,
                'customer_id' => $customer->id,
            ]
        );

        $design = Design::find($invoice->design_id);

        if (!empty(request()->input('design'))) {
            $design_object = json_decode(
                json_encode(request()->input('design'))
            );

            $design->design = $design_object->design;
        }

        $objPdf = new InvoicePdf($invoice);

        $invoice->forceDelete();
        $contact->forceDelete();
        $customer->forceDelete();

        DB::rollBack();

        $data = CreatePdf::dispatchNow($objPdf, $invoice, $contact, true, $show_html);

        if ($show_html === true) {
            return response()->json(['data' => $data]);
        }

        return response()->json(['data' => base64_encode(file_get_contents($data))]);
    }
}
