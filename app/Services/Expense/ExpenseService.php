<?php

namespace App\Services\Expense;

use App\Events\Expense\ExpenseWasApproved;
use App\Events\Quote\PurchaseOrderWasApproved;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use App\Services\Quote\MarkSent;
use App\Services\ServiceBase;
use Carbon\Carbon;

class ExpenseService extends ServiceBase
{
    /**
     * @var Expense
     */
    protected Expense $expense;

    public function __construct(Expense $expense)
    {
        $config = [
            'email'   => false,
            'archive' => false
        ];

        parent::__construct($expense, $config);
        $this->expense = $expense;
    }

    /**
     * @param ExpenseRepository $expense_repository
     * @return Expense|null
     */
    public function approve(ExpenseRepository $expense_repository): ?Expense
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
        $this->trigger($subject, $body, $expense_repository);

        return $this->expense;
    }
}
