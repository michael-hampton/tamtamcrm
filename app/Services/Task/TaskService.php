<?php

namespace App\Services\Task;

use App\Components\Pdf\TaskPdf;
use App\Factory\TimerFactory;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Task;
use App\Models\Timer;
use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceSum;
use App\Repositories\TaskRepository;
use App\Repositories\TimerRepository;
use App\Services\EntityManager;
use App\Services\ServiceBase;
use App\Services\Task\ConvertLead;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class TaskService
 * @package App\Services\Task
 */
class TaskService extends ServiceBase
{
    protected $task;

    /**
     * TaskService constructor.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        parent::__construct($task);
        $this->task = $task;
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return void
     */
    public function sendEmail($contact = null, $subject = '', $body = '', $template = 'deal')
    {
        return (new TaskEmail($this->task, $subject, $body))->execute();
    }

    /**
     * @param Request $request
     * @param CustomerRepository $customer_repo
     * @param TaskRepository $task_repo
     * @param bool $is_lead
     * @return mixed
     */
    public function updateLead(
        Request $request,
        CustomerRepository $customer_repo,
        TaskRepository $task_repo,
        $is_lead = true
    ) {
        $update_lead = new UpdateLead($this->task, $request, $customer_repo, $task_repo, $is_lead);

        $this->task = $update_lead->execute();
        return $this->task;
    }

    public function setTaskType($task_type)
    {
        $this->task->task_type = $task_type;

        return $this;
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->task->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new TaskPdf($this->task)), $this->task, $contact, $update);
    }

    /**
     * @param array $timers
     * @param Task $task
     * @param TimerRepository $timer_repository
     * @return Timer|null
     */
    public function saveTimers(array $timers, Task $task, TimerRepository $timer_repository)
    {
        $task->timers()->forceDelete();

        foreach ($timers as $time) {
            $timer = $timer_repository->save(
                $task,
                TimerFactory::create(
                    auth()->user(),
                    auth()->user()->account_user()->account,
                    $task
                ),
                $time
            );
        }

        return $timer;
    }

    /**
     * @return Task|null
     */
    public function save(): ?Task
    {
        $this->task->save();
        return $this->task;
    }
}
