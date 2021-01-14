<?php

namespace App\Factory;

use App\Models\Email;

class EmailFactory
{
    /**
     * @param int $user_id
     * @param int $account_id
     * @return Email
     */
    public static function create(int $user_id, int $account_id): Email
    {
        $email = new Email();
        $email->user_id = $user_id;
        $email->recipient = '';
        $email->recipient_email = '';
        $email->subject = '';
        $email->body = '';
        $email->design = '';
        $email->entity = '';
        $email->entity_id = null;
        $email->direction = 'OUT';
        $email->account_id = $account_id;

        return $email;
    }
}
