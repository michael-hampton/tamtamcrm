<?php

namespace App\Transformations;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use App\Repositories\MessageRepository;

trait MessageUserTransformable
{

    /**
     *
     * @param Customer $customer
     * @param User $currentUser
     * @return Customer
     */
    protected function transformUser(Customer $customer, User $currentUser)
    {
        $message = (new MessageRepository(new Message))->getMessagesForCustomer($customer, $currentUser, true);

        $prop = new Customer;

        $prop->customer_id = (int)$customer->id;
        $prop->name = $customer->first_name . ' ' . $customer->last_name;
        $prop->avatar = '';

        if (!empty($message)) {
            $prop->message = $message->message;
            $prop->when = $message->created_at;
            $prop->seen = false;
        }

        return $prop;
    }

}
