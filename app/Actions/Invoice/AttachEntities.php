<?php

namespace App\Actions\Invoice;


use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Task;

class AttachEntities
{
    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function attach()
    {
        if (empty($this->invoice->line_items)) {
            return true;
        }

        $entities_added = [];

        foreach ($this->invoice->line_items as $line_item) {
            if ($line_item->type_id === Invoice::EXPENSE_TYPE) {
                $expense = Expense::where('id', '=', $line_item->product_id)->first();

                if (!$expense || $expense->status_id === Expense::STATUS_INVOICED) {
                    continue;
                }

                $expense->setStatus(Expense::STATUS_INVOICED);
                $expense->invoice_id = $this->invoice->id;
                $expense->save();

                $entities_added['expenses'][] = $expense;
            }

            if ($line_item->type_id === Invoice::TASK_TYPE) {
                $task = Task::where('id', '=', $line_item->product_id)->first();

                if (!$task || $task->task_status_id === Task::STATUS_INVOICED) {
                    continue;
                }

                //$task->setStatus(Task::STATUS_INVOICED);
                $task->invoice_id = $this->invoice->id;
                $task->save();

                $entities_added['tasks'][] = $task;
            }
        }

        return $entities_added;
    }

}