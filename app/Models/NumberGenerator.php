<?php

namespace App\Models;

use App\Jobs\ResetNumbers;
use Exception;
use ReflectionClass;
use ReflectionException;

class NumberGenerator
{
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

        $this->setType($entity_obj, $customer)->setCounterEntity($customer)->setCounter($customer)->setPattern(
            $entity_obj,
            $customer
        )->setPrefix($customer);

        $padding = $customer !== null ? $customer->getSetting(
            'counter_padding'
        ) : $entity_obj->account->settings->counter_padding;

        $number = $this->checkEntityNumber($resource, $customer, $this->counter, $padding);

        $this->updateEntityCounter();

        ResetNumbers::dispatchNow($this->entity_obj, true, false);

        return $number;
    }

    private function setPrefix(Customer $customer = null)
    {
        $this->recurring_prefix = $customer !== null ? $customer->getSetting(
            'recurring_number_prefix'
        ) : $this->entity_obj->account->settings->recurring_number_prefix;

        return $this;
    }

    private function setPattern($entity_object, Customer $customer = null)
    {
        $entity_id = strtolower((new ReflectionClass($entity_object))->getShortName());

        $pattern_entity = "{$entity_id}_number_prefix";

        $this->pattern = $customer !== null
            ? trim($customer->getSetting($pattern_entity))
            : trim(
                $this->entity_obj->account->settings->{$pattern_entity}
            );

        return $this;
    }

    private function setCounter(Customer $customer = null)
    {
        if ($this->counter_type === 'group' && $customer !== null) {
            return !empty($customer->group_settings->{$this->counter_var}) ? $customer->group_settings->{$this->counter_var} : 1;
        }

        $this->counter = $customer !== null ? $customer->getSetting(
            $this->counter_var
        ) : $this->entity_obj->account->settings->{$this->counter_var};

        if (empty($this->counter)) {
            $this->counter = 1;
        }

        return $this;
    }

    private function setCounterEntity(Customer $customer = null)
    {
        if ($this->counter_type === 'group' && $customer !== null) {
            $this->counter_entity = $customer->group_settings;
            return $this;
        }

        if (empty($customer)) {
            $this->counter_entity = $this->entity_obj->account;
            return $this;
        }

        $this->counter_entity = $customer;

        return $this;
    }

    /**
     * @param $entity_object
     * @param Customer|null $customer
     * @return $this
     * @throws ReflectionException
     */
    private function setType($entity_object, Customer $customer = null)
    {
        $entity_id = strtolower((new ReflectionClass($entity_object))->getShortName());
        $this->counter_var = "{$entity_id}_number_counter";
        $counter_type = "{$entity_id}_counter_type";

        $this->counter_type = $customer !== null ? $customer->getSetting(
            $counter_type
        ) : $this->entity_obj->account->settings->{$counter_type};

        return $this;
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

            $check = $class::whereAccountId($this->entity_obj->account->id)
                ->whereNumber($number)
                ->withTrashed()
                ->first();

            $counter++;
        } while ($check);
        return $number;
    }

    private function formatPrefix($number, Customer $customer = null)
    {
        $prefix = '';

        $pattern = explode(':', $this->pattern);

        switch ($pattern[0]) {
            case 'YEAR':
                $prefix = date('Y');
                break;
            case 'DATE':
                $prefix = !empty($pattern[1]) ? date($pattern[1]) : date('d-m-Y');
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

            case 'USER':
                if (!empty($this->entity_obj->user_id)) {
                    $prefix = $this->entity_obj->user_id;
                }
                break;
            default:
                $prefix = $pattern[0];
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
