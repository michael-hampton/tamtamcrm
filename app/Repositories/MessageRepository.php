<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\MessageRepositoryInterface;
use Exception;

/**
 * Description of MessageRepository
 *
 * @author michael.hampton
 */
class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{

    /**
     * MessageRepository constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        parent::__construct($message);
        $this->model = $message;
    }

    /**
     * Create the message
     *
     * @param array $data
     *
     * @return Message
     */
    public function createMessage(array $data): Message
    {
        return $this->create($data);
    }

    /**
     * Delete a message
     *
     * @return bool
     * @throws Exception
     */
    public function deleteMessage(): bool
    {
        return $this->delete();
    }

    /**
     *
     * @param Customer $customer
     * @param User $user
     * @param bool $blLastOnly
     * @return type
     */
    public function getMessagesForCustomer(Customer $customer, User $user, $blLastOnly = false)
    {
        $query = Message::where('customer_id', '=', $customer->id);

        if (!is_null($user)) {
            $query->where('user_id', '=', $user->id);
        }

        if ($blLastOnly) {
            $query->orderBy('id', 'desc')->limit(1);
            return $query->first();
        }

        return $query->get();
    }

}
