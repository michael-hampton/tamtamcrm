<?php

namespace App\Events\Customer;

use App\Models\Customer;
use App\Traits\SendSubscription;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;

/**
 * Class CustomerWasCreated.
 */
class CustomerWasCreated implements PerformsMetrics
{
    use SerializesModels;
    use Dispatchable;
    use Measurable;
    use SendSubscription;

    /**
     * @var Customer
     */
    public $customer;
    protected $meter = 'customer-created';

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
