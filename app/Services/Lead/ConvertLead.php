<?php

namespace App\Services\Lead;

use App\Factory\Lead\CloneLeadToAddressFactory;
use App\Factory\Lead\CloneLeadToContactFactory;
use App\Factory\Lead\CloneLeadToCustomerFactory;
use App\Factory\Lead\CloneLeadToDealFactory;
use App\Factory\Lead\CloneLeadToTaskFactory;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class ConvertLead
 * @package App\Services\Task
 */
class ConvertLead
{
    private $lead;

    /**
     * ConvertLead constructor.
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function execute()
    {
        if ($this->lead->task_status_id === Lead::STATUS_COMPLETED) {
            return false;
        }

        try {
            DB::beginTransaction();
            $customer = CloneLeadToCustomerFactory::create($this->lead, $this->lead->user, $this->lead->account);

            if (!$customer->save()) {
                DB::rollback();
                return null;
            }

            $address = CloneLeadToAddressFactory::create($this->lead, $customer);

            if (!$address->save()) {
                DB::rollback();
                return null;
            }

            $client_contact =
                CloneLeadToContactFactory::create($this->lead, $customer, $this->lead->user, $this->lead->account);


            if (!$client_contact->save()) {
                DB::rollback();
                return null;
            }

            if (!$this->createTask($customer)) {
                return null;
            }

            if (!$this->createDeal($customer)) {
                return null;
            }

            $this->lead->task_status_id = Lead::STATUS_COMPLETED;

            if (!$this->lead->save()) {
                DB::rollback();
                return null;
            }

            DB::commit();

            return $this->lead;
        } catch (Exception $e) {
            DB::rollback();
            return null;
        }
    }

    /**
     * @param Customer $customer
     * @return Task|null
     */
    private function createTask(Customer $customer): ?Task
    {
        $task = CloneLeadToTaskFactory::create($this->lead, $this->lead->user, $this->lead->account, $customer);

        $date = new DateTime(); // Y-m-d
        $date->add(new DateInterval('P30D'));
        $due_date = $date->format('Y-m-d');

        $task->due_date = $due_date;

        if (!$task->save()) {
            DB::rollback();
            return null;
        }

        return $task;
    }

    /**
     * @param Customer $customer
     * @return Deal|null
     */
    private function createDeal(Customer $customer): ?Deal
    {
        $deal = CloneLeadToDealFactory::create($this->lead, $this->lead->user, $this->lead->account, $customer);

        $date = new DateTime(); // Y-m-d
        $date->add(new DateInterval('P30D'));
        $due_date = $date->format('Y-m-d');

        $deal->due_date = $due_date;

        if (!$deal->save()) {
            DB::rollback();
            return null;
        }

        return $deal;
    }
}
