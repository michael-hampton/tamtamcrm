<?php

namespace App\Mail\Admin;

use App\Models\Expense;
use App\Models\User;
use App\ViewModels\AccountViewModel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ExpenseApproved extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Expense
     */
    private Expense $expense;

    /**
     * ExpenseApproved constructor.
     * @param Expense $expense
     * @param User $user
     */
    public function __construct(Expense $expense, User $user)
    {
        parent::__construct('expense_approved', $expense);

        $this->expense = $expense;
        $this->entity = $expense;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return void
     */
    public function build()
    {
        $data = $this->getData();

        $this->setSubject($data);
        $this->setMessage($data);
        $this->buildButton();
        $this->execute();
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'total'   => $this->expense->getFormattedTotal(),
            'expense' => $this->expense->getNumber(),
        ];
    }

    /**
     * @return array
     */
    private function buildButton(): void
    {
        $this->button = [
            'url'         => config('taskmanager.web_url') . '/#/expenses?id=' . $this->expense->id,
            'button_text' => trans('texts.view_expense'),
        ];
    }
}
