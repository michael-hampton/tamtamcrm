<?php

namespace App\Transformations;

use App\Models\Email;

class EmailTransformable
{

    /**
     * @param Email $email
     * @return array
     */
    public function transformEmail(Email $email)
    {
        return [
            'id'              => (int)$email->id,
            'entity'          => $email->entity,
            'entity_id'       => (int)$email->entity_id,
            'subject'         => (string)$email->subject,
            'body'            => $email->body,
            'recipient'       => $email->recipient ?: '',
            'recipient_email' => $email->recipient_email ?: '',
            'sent_at'         => $email->sent_at ?: '',
            'design'          => $email->design ?: '',
            'updated_at'      => $email->updated_at,
            'archived_at'     => $email->deleted_at,
        ];
    }

}
