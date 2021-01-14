<?php

namespace App\Transformations;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;

trait MessageTransformable
{

    /**
     *
     * @param Message $message
     * @param User $currentUser
     * @param Customer $customer
     * @return Message
     */
    protected function transformMessage(Message $message, User $currentUser, Customer $customer)
    {
        $prop = new Message;
        $author = $message->direction === 1 ? $currentUser->first_name . ' ' .
            $currentUser->last_name : $customer->first_name . ' ' . $customer->last_name;

        $prop->author = $author;
        $prop->avatar = '';
        $prop->message = $message->message;
        $prop->when = $message->created_at;

        return $prop;
    }

}
