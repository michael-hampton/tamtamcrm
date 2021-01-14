<?php

namespace App\Jobs\Expense;

use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use App\Traits\CalculateRecurring;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRecurringExpense implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CalculateRecurring;

    /**
     * @var Expense
     */
    private Expense $expense;

    /**
     * @var ExpenseRepository
     */
    private ExpenseRepository $expense_repo;

    /**
     * SendRecurringExpense constructor.
     * @param ExpenseRepository $expense_repo
     */
    public function __construct(ExpenseRepository $expense_repo)
    {
        $this->expense_repo = $expense_repo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processRecurringExpenses();
    }

    private function processRecurringExpenses()
    {
        $recurring_expenses = Expense::whereDate('next_send_date', '=', Carbon::today())
                                     ->whereDate('date', '!=', Carbon::today())
                                     ->whereDate('recurring_start_date', '<=', Carbon::today())
                                     ->where(
                                         function ($query) {
                                             $query->whereNull('recurring_end_date')
                                                   ->orWhere('recurring_end_date', '>=', Carbon::today());
                                         }
                                     )
                                     ->get();

        foreach ($recurring_expenses as $recurring_expense) {
            if ($recurring_expense->start_date->gt(Carbon::now()) || Carbon::now()->gt(
                    $recurring_expense->end_date
                )) {
                continue;
            }

            $expense = $recurring_expense->replicate();
            $expense = $this->expense_repo->save(['recurring_expense_id' => $recurring_expense->id], $expense);

            $expense->service()->sendEmail(null, trans('texts.quote_subject'), trans('texts.quote_body'));

            $recurring_expense->last_sent_date = Carbon::today();
            $recurring_expense->next_send_date = $this->calculateDate($recurring_expense->frequency);
            $recurring_expense->save();
        }
    }
}
