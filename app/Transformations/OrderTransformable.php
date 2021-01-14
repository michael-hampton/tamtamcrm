<?php

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\OrderInvitation;

trait OrderTransformable
{
    /**
     * @param Order $order
     * @return array
     */
    protected function transformOrder(Order $order)
    {
        return [
            'id'                  => (int)$order->id,
            'number'              => $order->number ?: '',
            'created_at'          => $order->created_at,
            'user_id'             => (int)$order->user_id,
            'account_id'          => (int)$order->account_id,
            'project_id'          => (int)$order->project_id,
            'assigned_to'         => (int)$order->assigned_to,
            'company_id'          => (int)$order->company_id ?: null,
            'currency_id'         => (int)$order->currency_id ?: null,
            'exchange_rate'       => (float)$order->exchange_rate,
            'public_notes'        => $order->public_notes ?: '',
            'private_notes'       => $order->private_notes ?: '',
            'customer_id'         => (int)$order->customer_id,
            'date'                => $order->date ?: '',
            'due_date'            => $order->due_date ?: '',
            'design_id'           => (int)$order->design_id,
            'invitations'         => $this->transformOrderInvitations($order->invitations),
            'total'               => $order->total,
            'balance'             => (float)$order->balance,
            'sub_total'           => (float)$order->sub_total,
            'tax_total'           => (float)$order->tax_total,
            'status_id'           => (int)$order->status_id,
            'invoice_id'          => (int)$order->invoice_id,
            'discount_total'      => (float)$order->discount_total,
            'deleted_at'          => $order->deleted_at,
            'terms'               => (string)$order->terms ?: '',
            'footer'              => (string)$order->footer ?: '',
            'line_items'          => $order->line_items ?: (array)[],
            'custom_value1'       => (string)$order->custom_value1 ?: '',
            'custom_value2'       => (string)$order->custom_value2 ?: '',
            'custom_value3'       => (string)$order->custom_value3 ?: '',
            'custom_value4'       => (string)$order->custom_value4 ?: '',
            'transaction_fee'     => (float)$order->transaction_fee,
            'shipping_cost'       => (float)$order->shipping_cost,
            'gateway_fee'         => (float)$order->gateway_fee,
            'gateway_percentage'  => (bool)$order->gateway_percentage,
            'transaction_fee_tax' => (bool)$order->transaction_fee_tax,
            'shipping_cost_tax'   => (bool)$order->shipping_cost_tax,
            'emails'              => $this->transformOrderEmails($order->emails()),
            'audits'              => $this->transformAuditsForOrder($order->audits),
            'files'               => $this->transformOrderFiles($order->files),
            'tax_rate'            => (float)$order->tax_rate,
            'tax_2'               => (float)$order->tax_2,
            'tax_3'               => (float)$order->tax_3,
            'tax_rate_name'       => $order->tax_rate_name,
            'tax_rate_name_2'     => $order->tax_rate_name_2,
            'tax_rate_name_3'     => $order->tax_rate_name_3,
            'viewed'              => (bool)$order->viewed,
            'is_deleted'          => (bool)$order->is_deleted,
        ];
    }

    /**
     * @param $invitations
     * @return array
     */
    private function transformOrderInvitations($invitations)
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
    private function transformOrderEmails($emails)
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

    public function transformAuditsForOrder($audits)
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
    private function transformOrderFiles($files)
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
