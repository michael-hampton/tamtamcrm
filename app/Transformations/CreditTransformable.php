<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 02/12/2019
 * Time: 15:58
 */

namespace App\Transformations;

use App\Models\Audit;
use App\Models\Credit;
use App\Models\CreditInvitation;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;


trait CreditTransformable
{
    /**
     * @param Credit $credit
     * @return array
     */
    protected function transformCredit(Credit $credit)
    {
        return [
            'id'                  => (int)$credit->id,
            'number'              => $credit->number ?: '',
            'created_at'          => $credit->created_at,
            'user_id'             => (int)$credit->user_id,
            'account_id'          => (int)$credit->account_id,
            'project_id'          => (int)$credit->project_id,
            'assigned_to'         => (int)$credit->assigned_to,
            'company_id'          => (int)$credit->company_id ?: null,
            'currency_id'         => (int)$credit->currency_id ?: null,
            'exchange_rate'       => (float)$credit->exchange_rate,
            'public_notes'        => $credit->public_notes ?: '',
            'private_notes'       => $credit->private_notes ?: '',
            'customer_id'         => (int)$credit->customer_id,
            'date'                => $credit->date ?: '',
            'due_date'            => $credit->due_date ?: '',
            'design_id'           => (int)$credit->design_id,
            'invitations'         => $this->transformCreditInvitations($credit->invitations),
            'total'               => $credit->total,
            'balance'             => (float)$credit->balance,
            'sub_total'           => (float)$credit->sub_total,
            'tax_total'           => (float)$credit->tax_total,
            'status_id'           => (int)$credit->status_id,
            'discount_total'      => (float)$credit->discount_total,
            'deleted_at'          => $credit->deleted_at,
            'terms'               => (string)$credit->terms ?: '',
            'footer'              => (string)$credit->footer ?: '',
            'line_items'          => $credit->line_items ?: (array)[],
            'custom_value1'       => (string)$credit->custom_value1 ?: '',
            'custom_value2'       => (string)$credit->custom_value2 ?: '',
            'custom_value3'       => (string)$credit->custom_value3 ?: '',
            'custom_value4'       => (string)$credit->custom_value4 ?: '',
            'transaction_fee'     => (float)$credit->transaction_fee,
            'shipping_cost'       => (float)$credit->shipping_cost,
            'gateway_fee'         => (float)$credit->gateway_fee,
            'gateway_percentage'  => (bool)$credit->gateway_percentage,
            'transaction_fee_tax' => (bool)$credit->transaction_fee_tax,
            'shipping_cost_tax'   => (bool)$credit->shipping_cost_tax,
            'emails'              => $this->transformCreditEmails($credit->emails()),
            'audits'              => $this->transformAuditsForCredit($credit->audits),
            'files'               => $this->transformCreditFiles($credit->files),
            'tax_rate'            => (float)$credit->tax_rate,
            'tax_2'               => (float)$credit->tax_2,
            'tax_3'               => (float)$credit->tax_3,
            'tax_rate_name'       => $credit->tax_rate_name,
            'tax_rate_name_2'     => $credit->tax_rate_name_2,
            'tax_rate_name_3'     => $credit->tax_rate_name_3,
            'viewed'              => (bool)$credit->viewed,
            'is_deleted'          => (bool)$credit->is_deleted,
        ];
    }

    /**
     * @param $invitations
     * @return array
     */
    private function transformCreditInvitations($invitations)
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
    private function transformCreditEmails($emails)
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

    public function transformAuditsForCredit($audits)
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
    private function transformCreditFiles($files)
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
