<?php

namespace App\Services\Task;

use App\Repositories\CustomerRepository;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

/**
 * Class UpdateLead
 * @package App\Services\Task
 */
class UpdateLead
{
    private $task;
    private $request;
    private $customer_repo;
    private $task_repo;
    private $is_lead;

    /**
     * UpdateLead constructor.
     * @param $task
     * @param Request $request
     * @param CustomerRepository $customer_repo
     * @param TaskRepository $task_repo
     * @param $is_lead
     */
    public function __construct(
        $task,
        Request $request,
        CustomerRepository $customer_repo,
        TaskRepository $task_repo,
        $is_lead
    ) {
        $this->task = $task;
        $this->request = $request;
        $this->customer_repo = $customer_repo;
        $this->task_repo = $task_repo;
        $this->is_lead = $is_lead;
    }

    public function execute()
    {
        $customer = $this->task->customer;

        $this->customer_repo->save(
            $this->request->only('first_name', 'last_name', 'email', 'phone', 'job_title'),
            $customer
        );

        if ($this->request->has('address_1') && !empty($this->request->address_1)) {
            $address = $customer->addresses()->where('address_type', 1)->first();

            if ($address) {
                $address->update(
                    [
                        'address_1'  => $this->request->address_1,
                        'address_2'  => $this->request->address_2,
                        'zip'        => $this->request->zip,
                        'city'       => $this->request->city,
                        'country_id' => 225,
                        'status'     => 1
                    ]
                );
            } else {
                $customer->addresses()->create(
                    [
                        'address_1'  => $this->request->address_1,
                        'address_2'  => $this->request->address_2,
                        'zip'        => $this->request->zip,
                        'city'       => $this->request->city,
                        'country_id' => 225,
                        'status'     => 1
                    ]
                );
            }
        }

        $this->task_repo->save($this->request->only('source_type', 'name', 'description', 'valued_at'), $this->task);

        if ($this->request->has('contributors')) {
            $this->task->users()->sync($this->request->input('contributors'));
        }

        return $this->task;
    }
}
