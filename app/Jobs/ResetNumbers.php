<?php

namespace App\Jobs;

use App\Traits\CalculateRecurring;
use App\Traits\CreditPayment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;
use ReflectionException;

class ResetNumbers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreditPayment, CalculateRecurring;

    /**
     * @var array|string[]
     */
    private array $availiable_reset_types = [
        'task',
        'project',
        'invoice',
        'credit',
        'order',
        'expense',
        'company',
        'lead',
        'case',
        'payment',
        'customer',
        'recurringinvoice',
        'recurringquote',
        'quote',
        'deal',
        'purchaseorder'

    ];

    private $entity;

    private bool $reset_all;

    private bool $reset_customer;

    /**
     * ResetNumbers constructor.
     * @param $entity
     * @param bool $reset_all
     * @param bool $reset_customer
     */
    public function __construct($entity, bool $reset_all = true, bool $reset_customer = true)
    {
        $this->entity = $entity;
        $this->reset_all = $reset_all;
        $this->reset_customer = $reset_customer;
    }

    public function handle()
    {
        $this->resetCounters();
    }

    /**
     * @return bool
     * @throws ReflectionException
     */
    public function resetCounters(): bool
    {
        $date_to_reset = !empty($this->entity->customer) ? $this->entity->customer->getSetting(
            'date_counter_next_reset'
        ) : $this->entity->account->settings->{'date_counter_next_reset'};

        $frequency_type = !empty($this->entity->customer) ? $this->entity->customer->getSetting(
            'counter_frequency_type'
        ) : $this->entity->account->settings->{'counter_frequency_type'};

        if (!Carbon::parse($date_to_reset)->isToday()) {
            return false;
        }

        $next_date_to_send = $this->calculateDate($frequency_type)->format('Y-m-d');

        if (!$this->reset_all) {
            $entity_id = strtolower((new ReflectionClass($this->entity))->getShortName());
            $counter_var = "{$entity_id}_number_counter";
            $this->resetVariable($counter_var, $next_date_to_send);
        }

        foreach ($this->availiable_reset_types as $availiable_reset_type) {
            $counter_var = "{$availiable_reset_type}_number_counter";
            $this->resetVariable($counter_var, $next_date_to_send);
        }

        return true;
    }

    /**
     * @param string $variable
     * @param string $next_send_date
     * @return bool
     */
    private function resetVariable(string $variable, string $next_send_date): bool
    {
        if (!empty($this->entity->customer) && $this->reset_customer) {
            $this->entity->customer->settings->{$variable} = 1;
            $this->entity->customer->settings->date_counter_next_reset = $next_send_date;
            $this->entity->customer->save();
        }

        $this->entity->account->settings->{$variable} = 1;
        $this->entity->account->settings->date_counter_next_reset = $next_send_date;
        $this->entity->account->save();

        return true;
    }

}
