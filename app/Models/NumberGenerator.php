<?php

namespace App\Models;

use App\Jobs\ResetNumbers;
use App\Traits\CalculateRecurring;
use Exception;
use ReflectionClass;
use ReflectionException;

class NumberGenerator
{
    use CalculateRecurring;

    private $entity_obj;

    /**
     * @var string
     */
    private $counter_entity;

    private $counter_var;

    /**
     * @var string
     */
    private string $pattern = '';

    /**
     * @var int
     */
    private int $counter = 0;

    /**
     * @param Customer $customer
     * @param $entity_obj
     * @return string
     * @throws Exception
     */
    public function getNextNumberForEntity($entity_obj, Customer $customer = null): string
    {
        $this->entity_obj = $entity_obj;
        $resource = get_class($entity_obj);

        $this->setType($entity_obj, $resource, $customer);
        $this->setPrefix($customer);

        $padding = $customer !== null ? $customer->getSetting(
            'counter_padding'
        ) : $entity_obj->account->settings->counter_padding;

        $number = $this->checkEntityNumber($resource, $customer, $this->counter, $padding);

        $this->updateEntityCounter();

        ResetNumbers::dispatchNow($this->entity_obj, true, false);

        return $number;
    }

    /**
     * @param $entity_object
     * @param $resource
     * @param Customer|null $customer
     * @return bool
     * @throws ReflectionException
     */
    private function setType($entity_object, $resource, Customer $customer = null)
    {
        $entity_id = strtolower((new ReflectionClass($entity_object))->getShortName());
        $pattern_entity = "{$entity_id}_number_prefix";
        $this->counter_var = "{$entity_id}_number_counter";
        $counter_type = "{$entity_id}_counter_type";

        $this->pattern = $customer !== null
            ? trim($customer->getSetting($pattern_entity))
            : trim(
                $this->entity_obj->account->settings->{$pattern_entity}
            );

        $this->counter = $customer !== null ? $customer->getSetting(
            $this->counter_var
        ) : $this->entity_obj->account->settings->{$this->counter_var};

        $this->counter_type = $customer !== null ? $customer->getSetting(
            $counter_type
        ) : $this->entity_obj->account->settings->{$counter_type};

        $this->counter_entity = $this->entity_obj->account;

        if ($customer === null) {
            return true;
        }

        if ($resource === Customer::class || $this->counter_type === 'customer') {
            $this->counter = $customer->getSetting($this->counter_var);
            $this->counter_entity = $customer;
        } elseif ($this->counter_type === 'group') {
            $this->counter = $customer->group_settings->{$this->counter_var};
            $this->counter_entity = $customer->group_settings;
        }

        return true;
    }

    private function setPrefix(Customer $customer = null)
    {
        $this->recurring_prefix = $customer !== null ? $customer->getSetting(
            'recurring_number_prefix'
        ) : $this->entity_obj->account->settings->recurring_number_prefix;
    }

    private function checkEntityNumber($class, $customer, $counter, $padding)
    {
        $check = false;
        do {
            $number = str_pad($counter, $padding, '0', STR_PAD_LEFT);
            $number = $this->formatPrefix($number, $customer);

            if ($this->isRecurring($class)) {
                $number = $this->addPrefixToCounter($number, $class, $customer);
            }

            $check = $class::whereAccountId($this->entity_obj->account->id)->whereNumber($number)->withTrashed()->first(
            );

            $counter++;
        } while ($check);
        return $number;
    }

    private function formatPrefix($number, Customer $customer = null)
    {
        $prefix = '';

        switch ($this->pattern) {
            case 'YEAR':
                $prefix = date('Y');
                break;
            case 'DATE':
                $prefix = date('d-m-Y');
                break;
            case 'MONTH':
                $prefix = date('M');
                break;
            case 'CUSTOMER':
                if (!empty($customer)) {
                    $prefix = $customer->number;
                }

                break;
            case 'COMPANY':
                if (!empty($this->entity_obj->company)) {
                    $prefix = $this->entity_obj->company->number;
                }

                break;
        }

        return !empty($prefix) ? "{$prefix}-{$number}" : $number;
    }

    private function isRecurring($resource)
    {
        return in_array($resource, [RecurringInvoice::class, RecurringQuote::class]) && !empty($this->recurring_prefix);
    }

    /**
     * @param $number
     * @param $resource
     * @param Customer|null $customer
     * @return string
     */
    private function addPrefixToCounter($number, $resource, Customer $customer = null): string
    {
        if (!$this->isRecurring($resource)) {
            return $number;
        }

        return $this->recurring_prefix . $number;
    }

    private function updateEntityCounter(): void
    {
        $settings = $this->counter_entity->settings;
        $settings->{$this->counter_var} = !empty($settings->{$this->counter_var}) ? $settings->{$this->counter_var} + 1 : 1;
        $this->counter_entity->settings = $settings;
        $this->counter_entity->save();
    }
}
