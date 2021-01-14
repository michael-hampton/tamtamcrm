<?php

namespace App\Events\Customer;

use App\Models\Customer;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerWasUpdated.
 */
class CustomerWasUpdated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Customer
     */
    public $customer;

    /**
     * Create a new event instance.
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->send($customer, get_class($this));
    }
}
