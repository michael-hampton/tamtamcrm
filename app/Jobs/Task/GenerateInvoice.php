<?php

namespace App\Jobs\Task;

use App\Factory\CloneTaskToInvoiceFactory;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\Timer;
use App\Repositories\InvoiceRepository;
use App\Repositories\TimerRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tasks;

    private InvoiceRepository $invoice_repo;

    public function __construct(InvoiceRepository $invoice_repo, $tasks)
    {
        $this->tasks = $tasks;
        $this->invoice_repo = $invoice_repo;
    }

    public function handle()
    {
        $line_items = [];
        $customer = false;

        foreach ($this->tasks as $task) {
            if ($task === Task::STATUS_INVOICED) {
                continue;
            }

            $notes = $task->description . '\n';

            if (!empty($task->timers)) {
                foreach ($task->timers as $timer) {
                    if (empty($timer->started_at) || empty($timer->stopped_at)) {
                        continue;
                    }

                    $start = Carbon::parse($timer->started_at)->format('d-m-y H:i:s');
                    $end = Carbon::parse($timer->stopped_at)->format('d-m-y H:i:s');
                    $notes .= '\n### ' . $start . ' - ' . $end;
                }
            }

            $task_rate = 0;
            $total_duration = (new TimerRepository(new Timer()))->getTotalDuration($task);

            if (!empty($task->project)) {
                $task_rate = $task->project->task_rate;
            }

            if (empty($task_rate) || empty($total_duration)) {
                continue;
            }

            if (!empty($customer) && $task->customer_id !== $customer) {
                continue;
            }

            $line_items[] = [
                'product_id'    => $task->id,
                'unit_price'    => $task_rate > 0 ? $task_rate * $total_duration : 0,
                'quantity'      => round($total_duration, 3),
                'type_id'       => Invoice::TASK_TYPE,
                'notes'         => $notes,
                'unit_discount' => 0
            ];

            $task->setStatus(Task::STATUS_INVOICED);
            $task->save();

            $customer = $task->customer_id;
        }

        $first_task = $this->tasks->first();
        $invoice = CloneTaskToInvoiceFactory::create($first_task, $first_task->user, $first_task->account);
        $this->invoice_repo->createInvoice(['line_items' => $line_items], $invoice);
    }
}