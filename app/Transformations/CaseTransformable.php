<?php

namespace App\Transformations;


use App\Models\CaseInvitation;
use App\Models\Cases;
use App\Models\Email;
use App\Models\File;
use App\Models\Invitation;

trait CaseTransformable
{
    /**
     * @param Cases $cases
     * @return array
     */
    public function transform(Cases $cases)
    {
        $customer = $cases->customer;

        return [
            'id'                 => (int)$cases->id,
            'number'             => $cases->number ?: '',
            'message'            => $cases->message,
            'subject'            => $cases->subject,
            'private_notes'      => $cases->private_notes,
            'due_date'           => $cases->due_date ?: '',
            'account_id'         => (int)$cases->account_id,
            'customer_id'        => (int)$cases->customer_id,
            'contact_id'         => (int)$cases->contact_id,
            'customer_name'      => $customer->name,
            'user_id'            => (int)$cases->user_id,
            'assigned_to'        => (int)$cases->assigned_to,
            'parent_id'          => (int)$cases->parent_id,
            'link_project_value' => (int)$cases->link_project_value,
            'link_product_value' => (int)$cases->link_product_value,
            'status_id'          => (int)$cases->status_id,
            'category_id'        => (int)$cases->category_id,
            'category'           => $cases->category,
            'priority_id'        => (int)$cases->priority_id,
            'files'              => $this->transformCaseFiles($cases->files),
            'emails'             => $this->transformCaseEmails($cases->emails()),
            'updated_at'         => $cases->updated_at,
            'created_at'         => $cases->created_at,
            'is_deleted'         => (bool)$cases->is_deleted,
            'custom_value1'      => (string)$cases->custom_value1 ?: '',
            'custom_value2'      => (string)$cases->custom_value2 ?: '',
            'custom_value3'      => (string)$cases->custom_value3 ?: '',
            'custom_value4'      => (string)$cases->custom_value4 ?: '',
            'invitations'        => $this->transformCaseInvitations($cases->invitations),
        ];
    }

    /**
     * @param $files
     * @return array
     */
    private function transformCaseFiles($files)
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

    private function transformCaseEmails($emails)
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
     * @param $invitations
     * @return array
     */
    private function transformCaseInvitations($invitations)
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
}
