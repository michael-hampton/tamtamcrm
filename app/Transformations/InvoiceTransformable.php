<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;
use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use App\Models\Payment;

class InvoiceTransformable
{
    use PaymentTransformable;

    /**
     * @param Invoice $invoice
     * @return array
     */
    public function transformInvoice(Invoice $invoice)
    {
        return [
            'id'                   => (int)$invoice->id,
            'number'               => $invoice->number ?: '',
            'created_at'           => $invoice->created_at,
            'user_id'              => (int)$invoice->user_id,
            'account_id'           => (int)$invoice->account_id,
            'assigned_to'          => (int)$invoice->assigned_to,
            'company_id'           => (int)$invoice->company_id ?: null,
            'currency_id'          => (int)$invoice->currency_id ?: null,
            'exchange_rate'        => (float)$invoice->exchange_rate,
            'public_notes'         => $invoice->public_notes ?: '',
            'private_notes'        => $invoice->private_notes ?: '',
            'customer_id'          => (int)$invoice->customer_id,
            'date'                 => $invoice->date ?: '',
            'due_date'             => $invoice->due_date ?: '',
            'date_to_send'         => $invoice->date ?: '',
            'design_id'            => (int)$invoice->design_id,
            'invitations'          => $this->transformInvoiceInvitations($invoice->invitations),
            'total'                => $invoice->total,
            'balance'              => (float)$invoice->balance,
            'sub_total'            => (float)$invoice->sub_total,
            'tax_total'            => (float)$invoice->tax_total,
            'status_id'            => (int)$invoice->status_id,
            'discount_total'       => (float)$invoice->discount_total,
            'deleted_at'           => $invoice->deleted_at,
            'terms'                => (string)$invoice->terms ?: '',
            'footer'               => (string)$invoice->footer ?: '',
            'line_items'           => $invoice->line_items ?: (array)[],
            'custom_value1'        => (string)$invoice->custom_value1 ?: '',
            'custom_value2'        => (string)$invoice->custom_value2 ?: '',
            'custom_value3'        => (string)$invoice->custom_value3 ?: '',
            'custom_value4'        => (string)$invoice->custom_value4 ?: '',
            'transaction_fee'      => (float)$invoice->transaction_fee,
            'shipping_cost'        => (float)$invoice->shipping_cost,
            'gateway_fee'          => (float)$invoice->gateway_fee,
            'gateway_percentage'   => (bool)$invoice->gateway_percentage,
            'transaction_fee_tax'  => (bool)$invoice->transaction_fee_tax,
            'shipping_cost_tax'    => (bool)$invoice->shipping_cost_tax,
            'last_sent_date'       => $invoice->last_sent_date ?: '',
            'emails'               => $this->transformEmails($invoice->emails()),
            'paymentables'         => $this->transformInvoicePayments($invoice->payments),
            'audits'               => $this->transformAuditsForInvoice($invoice->audits),
            'files'                => $this->transformInvoiceFiles($invoice->files),
            'recurring_invoice_id' => $invoice->recurring_invoice_id,
            'recurring'            => $invoice->recurring_invoice,
            'tax_rate'             => (float)$invoice->tax_rate,
            'tax_2'                => (float)$invoice->tax_2,
            'tax_3'                => (float)$invoice->tax_3,
            'tax_rate_name'        => $invoice->tax_rate_name,
            'tax_rate_name_2'      => $invoice->tax_rate_name_2,
            'tax_rate_name_3'      => $invoice->tax_rate_name_3,
            'viewed'               => (bool)$invoice->viewed,
            'is_deleted'           => (bool)$invoice->is_deleted,
        ];
    }

    /**
     * @param $invitations
     * @return array
     */
    private function transformInvoiceInvitations($invitations)
    {
        if (empty($invitations)) {
            return [];
        }

        return $invitations->map(
            function (Invitation $invitation) {
                return (new InvitationTransformable())->transformInvitation($invitation);
            }
        )->all();
    }

    /**
     * @param $emails
     * @return array
     */
    private function transformEmails($emails)
    {
        if ($emails->count() === 0) {
            return [];
        }

        return $emails->map(
            function (Email $email) {
                return (new EmailTransformable())->transformEmail($email);
            }
        )->all();
    }

    public function transformInvoicePayments($paymentables)
    {
        if (empty($paymentables)) {
            return [];
        }

        return $paymentables->map(
            function (Payment $payment) {
                return $this->transformPayment($payment);
            }
        )->all();
    }

    public function transformAuditsForInvoice($audits)
    {
        if (empty($audits)) {
            return [];
        }

        return $audits->map(
            function (Audit $audit) {
                return (new AuditTransformable)->transformAudit($audit);
            }
        )->all();
    }

    /**
     * @param $files
     * @return array
     */
    private function transformInvoiceFiles($files)
    {
        if (empty($files)) {
            return [];
        }

        return $files->map(
            function (File $file) {
                return (new FileTransformable())->transformFile($file);
            }
        )->all();
    }
}
