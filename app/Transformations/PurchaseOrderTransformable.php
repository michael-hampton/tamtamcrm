<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvitation;

trait PurchaseOrderTransformable
{
    public function transformAuditsForPurchaseOrder($audits)
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
     * @param PurchaseOrder $po
     * @return array
     */
    protected function transformPurchaseOrder(PurchaseOrder $po, $files = null)
    {
        return [
            'number'              => $po->number ?: '',
            'id'                  => (int)$po->id,
            'created_at'          => $po->created_at,
            'user_id'             => (int)$po->user_id,
            'account_id'          => (int)$po->account_id,
            'project_id'          => (int)$po->project_id,
            'assigned_to'         => (int)$po->assigned_to,
            'company_id'          => (int)$po->company_id ?: null,
            'company_name'        => $po->company->name ?: '',
            'currency_id'         => (int)$po->currency_id ?: null,
            'exchange_rate'       => (float)$po->exchange_rate,
            'customer_note'       => $po->customer_note ?: '',
            'internal_note'       => $po->internal_note ?: '',
            'invoice_id'          => (int)$po->invoice_id,
            'date'                => $po->date ?: '',
            'due_date'            => $po->due_date ?: '',
            'design_id'           => (int)$po->design_id,
            'invitations'         => $this->transformPurchaseOrderInvitations($po->invitations),
            'total'               => $po->total,
            'balance'             => (float)$po->balance,
            'amount_paid'         => (float)$po->amount_paid,
            'sub_total'           => (float)$po->sub_total,
            'tax_total'           => (float)$po->tax_total,
            'status_id'           => (int)$po->status_id,
            'discount_total'      => (float)$po->discount_total,
            'deleted_at'          => $po->deleted_at,
            'terms'               => (string)$po->terms ?: '',
            'footer'              => (string)$po->footer ?: '',
            'line_items'          => $po->line_items ?: (array)[],
            'custom_value1'       => (string)$po->custom_value1 ?: '',
            'custom_value2'       => (string)$po->custom_value2 ?: '',
            'custom_value3'       => (string)$po->custom_value3 ?: '',
            'custom_value4'       => (string)$po->custom_value4 ?: '',
            'transaction_fee'     => (float)$po->transaction_fee,
            'shipping_cost'       => (float)$po->shipping_cost,
            'gateway_fee'         => (float)$po->gateway_fee,
            'gateway_percentage'  => (bool)$po->gateway_percentage,
            'transaction_fee_tax' => (bool)$po->transaction_fee_tax,
            'shipping_cost_tax'   => (bool)$po->shipping_cost_tax,
            'emails'              => $this->transformPurchaseOrderEmails($po->emails()),
            //'audits'              => $this->transformAuditsForPurchaseOrder($po->audits),
            'files'               => !empty($files) && !empty($files[$po->id]) ? $this->transformPurchaseOrderFiles(
                $files[$po->id]
            ) : [],
            'tax_rate'            => (float)$po->tax_rate,
            'tax_2'               => (float)$po->tax_2,
            'tax_3'               => (float)$po->tax_3,
            'tax_rate_name'       => $po->tax_rate_name,
            'tax_rate_name_2'     => $po->tax_rate_name_2,
            'tax_rate_name_3'     => $po->tax_rate_name_3,
            'viewed'              => (bool)$po->viewed,
            'hide'                => (bool)$po->hide,
        ];
    }

    /**
     * @param $invitations
     * @return array
     */
    private function transformPurchaseOrderInvitations($invitations)
    {
        if ($invitations->count() === 0) {
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
    private function transformPurchaseOrderEmails($emails)
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

    /**
     * @param $files
     * @return array
     */
    private function transformPurchaseOrderFiles($files)
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
