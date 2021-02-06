<?php

namespace App\Actions\Expense;


use App\Events\Expense\ExpenseWasApproved;
use App\Jobs\Email\SendEmail;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use Carbon\Carbon;

class Approve
{
    private Expense $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * @param ExpenseRepository $expense_repository
     * @return Expense|null
     */
    public function execute(ExpenseRepository $expense_repository): ?Expense
    {
        if ($this->expense->status_id != Expense::STATUS_LOGGED) {
            return null;
        }

        $this->expense->setStatus(Expense::STATUS_APPROVED);
        $this->expense->date_approved = Carbon::now();
        $this->expense->save();

        event(new ExpenseWasApproved($this->expense));

        // trigger
        $subject = trans('texts.expense_approved_subject');
        $body = trans('texts.expense_approved_body');

        SendEmail::dispatchNow($this->expense, $subject, $body);

        return $this->expense;
    }
}