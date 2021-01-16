<?php

namespace App\Transformations;

use App\Models\Invitation;

class InvitationTransformable
{

    /**
     * @param Invitation $invitation
     * @return array
     */
    public function transformInvitation(Invitation $invitation)
    {
        $key = (new \ReflectionClass($invitation->inviteable))->getShortName(
        ) === 'PurchaseOrder' ? 'company_id' : 'customer_id';

        return [
            'id'          => (int)$invitation->id,
            'contact_id'  => (int)$invitation->contact_id,
            $key          => $key === 'company_id' ? $invitation->company_contact->company_id : (int)$invitation->contact->customer_id,
            'key'         => $invitation->key,
            'sent_date'   => $invitation->sent_date ?: '',
            'viewed_date' => $invitation->viewed_date ?: '',
            'opened_date' => $invitation->opened_date ?: '',
            'updated_at'  => $invitation->updated_at,
            'archived_at' => $invitation->deleted_at,
        ];
    }
}
