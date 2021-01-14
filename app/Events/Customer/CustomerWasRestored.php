<?php

namespace App\Events\Customer;

use App\Models\Customer;
use Illuminate\Queue\SerializesModels;


class CustomerWasRestored
{
    use SerializesModels;

    /**
     * @var Customer
     */
    public Customer $customer;

    /**
     * CustomerWasRestored constructor.
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
