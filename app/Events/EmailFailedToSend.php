<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;
use robertogallea\LaravelMetrics\Models\Interfaces\PerformsMetrics;
use robertogallea\LaravelMetrics\Models\Traits\Measurable;

/**
 * Class InvoiceWasUpdated.
 */
class EmailFailedToSend implements PerformsMetrics
{
    use SerializesModels;
    use Measurable;

    /**
     * @var Invoice
     */
    public $entity;

    /**
     * @var array|string
     */
    public string $errors;

    protected $meter = 'email-failed-to-send';

    /**
     * Create a new event instance.
     *
     * @param $entity
     * @param string $errors
     */
    public function __construct($entity, string $errors = '')
    {
        $this->entity = $entity;
        $this->errors = $errors;
    }
}
