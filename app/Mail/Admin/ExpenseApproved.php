<?php

namespace App\Mail\Admin;

use App\Models\Expense;
use App\Models\User;
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
        $this->execute($this->buildMessage());
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
    private function buildMessage(): array
    {
        return [
            'title'       => $this->subject,
            'body'        => $this->message,
            'url'         => $this->getUrl() . 'expenses/' . $this->expense->id,
            'button_text' => trans('texts.view_expense'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => $this->expense->account->present()->logo(),
        ];
    }
}
