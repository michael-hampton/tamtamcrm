<?php

namespace App\Events\Expense;

use App\Models\Expense;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class ExpenseWasCreated
 * @package App\Events\RecurringInvoice
 */
class ExpenseWasCreated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Expense
     */
    public Expense $expense;

    /**
     * ExpenseWasCreated constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
        $this->send($expense, get_class($this));
    }
}
