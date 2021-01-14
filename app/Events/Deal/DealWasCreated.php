<?php

namespace App\Events\Deal;

use App\Models\Deal;
use App\Traits\SendSubscription;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;


/**
 * Class PaymentWasCreated.
 */
class DealWasCreated implements PerformsMetrics
{
    use SerializesModels;
    use Dispatchable;
    use Measurable;
    use SendSubscription;

    /**
     * @var array $payment
     */
    public $deal;
    protected $meter = 'deal-created';

    /**
     * DealWasCreated constructor.
     * @param Deal $deal
     */
    public function __construct(Deal $deal)
    {
        $this->deal = $deal;
        $this->send($deal, get_class($this));
    }
}
