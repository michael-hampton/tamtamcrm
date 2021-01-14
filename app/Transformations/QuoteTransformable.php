<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;
use App\Models\Quote;
use App\Models\QuoteInvitation;

class QuoteTransformable
{
    /**
     * @param Quote $quote
     * @return array
     */
    public function transformQuote(Quote $quote)
    {
        return [
            'id'                  => (int)$quote->id,
            'number'              => $quote->number ?: '',
            'created_at'          => $quote->created_at,
            'user_id'             => (int)$quote->user_id,
            'account_id'          => (int)$quote->account_id,
            'project_id'          => (int)$quote->project_id,
            'assigned_to'         => (int)$quote->assigned_to,
            'company_id'          => (int)$quote->company_id ?: null,
            'currency_id'         => (int)$quote->currency_id ?: null,
            'exchange_rate'       => (float)$quote->exchange_rate,
            'public_notes'        => $quote->public_notes ?: '',
            'private_notes'       => $quote->private_notes ?: '',
            'customer_id'         => (int)$quote->customer_id,
            'invoice_id'          => (int)$quote->invoice_id,
            'date'                => $quote->date ?: '',
            'due_date'            => $quote->due_date ?: '',
            'design_id'           => (int)$quote->design_id,
            'invitations'         => $this->transformQuoteInvitations($quote->invitations),
            'total'               => $quote->total,
            'balance'             => (float)$quote->balance,
            'sub_total'           => (float)$quote->sub_total,
            'tax_total'           => (float)$quote->tax_total,
            'status_id'           => (int)$quote->status_id,
            'discount_total'      => (float)$quote->discount_total,
            'deleted_at'          => $quote->deleted_at,
            'terms'               => (string)$quote->terms ?: '',
            'footer'              => (string)$quote->footer ?: '',
            'line_items'          => $quote->line_items ?: (array)[],
            'custom_value1'       => (string)$quote->custom_value1 ?: '',
            'custom_value2'       => (string)$quote->custom_value2 ?: '',
            'custom_value3'       => (string)$quote->custom_value3 ?: '',
            'custom_value4'       => (string)$quote->custom_value4 ?: '',
            'transaction_fee'     => (float)$quote->transaction_fee,
            'shipping_cost'       => (float)$quote->shipping_cost,
            'gateway_fee'         => (float)$quote->gateway_fee,
            'gateway_percentage'  => (bool)$quote->gateway_percentage,
            'transaction_fee_tax' => (bool)$quote->transaction_fee_tax,
            'shipping_cost_tax'   => (bool)$quote->shipping_cost_tax,
            'emails'              => $this->transformQuoteEmails($quote->emails()),
            'audits'              => $this->transformAuditsForQuote($quote->audits),
            'files'               => $this->transformQuoteFiles($quote->files),
            'recurring'           => $quote->recurring_quote,
            'recurring_quote_id'  => $quote->recurring_quote_id,
            'tax_rate'            => (float)$quote->tax_rate,
            'tax_2'               => (float)$quote->tax_2,
            'tax_3'               => (float)$quote->tax_3,
            'tax_rate_name'       => $quote->tax_rate_name,
            'tax_rate_name_2'     => $quote->tax_rate_name_2,
            'tax_rate_name_3'     => $quote->tax_rate_name_3,
            'viewed'              => (bool)$quote->viewed,
            'is_deleted'          => (bool)$quote->is_deleted,
        ];
    }

    /**
     * @param $invitations
     * @return array
     */
    private function transformQuoteInvitations($invitations)
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
    private function transformQuoteEmails($emails)
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

    public function transformAuditsForQuote($audits)
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
    private function transformQuoteFiles($files)
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
