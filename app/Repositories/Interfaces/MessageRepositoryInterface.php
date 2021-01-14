<?php

namespace App\Repositories\Interfaces;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;

/**
 *
 * @author michael.hampton
 */
interface MessageRepositoryInterface
{

    /**
     *
     * @param array $data
     * @return Message
     * @return Message
     */
    public function createMessage(array $data): Message;

    public function deleteMessage(): bool;

    /**
     *
     * @param Customer $customer
     * @param User $user
     * @param bool $blLastOnly
     */
    public function getMessagesForCustomer(Customer $customer, User $user, $blLastOnly = false);
}
