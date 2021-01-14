<?php

namespace App\Http\Controllers;

use App\Components\Pdf\InvoicePdf;
use App\Designs\PdfColumns;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Address;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Design;
use App\Models\Invoice;
use App\Traits\MakesInvoiceHtml;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class PreviewController extends Controller
{
    use MakesInvoiceHtml;

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
                return $this->blankEntity();
            }

            $file_path = $entity_obj->service()->generatePdf();

            return response()->json(['data' => base64_encode(file_get_contents($file_path))]);
        }

        return $this->blankEntity();
    }

    private function blankEntity()
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
                'user_id'     => auth()->user()->id,
                'account_id'  => auth()->user()->account_user()->account_id,
                'customer_id' => $customer->id,
                'is_primary'  => 1,
                'send_email'  => true,
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

        $design = Design::find($invoice->getDesignId());

        if (!empty(request()->input('design'))) {
            $design_object = json_decode(
                json_encode(request()->input('design'))
            );

            $design->design = $design_object->design;
        }

        $objPdf = new InvoicePdf($invoice);

        $designer =
            new PdfColumns(
                $objPdf, $invoice, $design, $invoice->account->settings->pdf_variables, 'invoice'
            );

        $file_path = $invoice->getPdfFilename();

        $invoice->forceDelete();
        $contact->forceDelete();
        $customer->forceDelete();

        DB::rollBack();

        $data = CreatePdf::dispatchNow($objPdf, $invoice, $contact, true);

        return response()->json(['data' => base64_encode(file_get_contents($data))]);
    }
}
