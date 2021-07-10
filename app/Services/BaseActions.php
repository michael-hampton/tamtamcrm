<?php


namespace App\Services;


use App\Services\Email\DispatchEmail;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;

class BaseActions
{

    protected function sendPaymentEmail(Invoice $invoice, InvoiceRepository $invoice_repo)
    {
        // trigger
        $subject = trans('texts.invoice_paid_subject');
        $body = trans('texts.invoice_paid_body');

        (new DispatchEmail($invoice))->execute(null, $subject, $body, 'invoice_paid');

        return true;
    }
}