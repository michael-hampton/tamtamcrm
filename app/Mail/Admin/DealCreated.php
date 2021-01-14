<?php

namespace App\Mail\Admin;

use App\Models\Deal;
use App\Models\User;
use App\Traits\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class DealCreated extends AdminMailer
{
    use Queueable, SerializesModels, Money;

    private Deal $deal;

    /**
     * DealCreated constructor.
     * @param Deal $deal
     * @param User $user
     */
    public function __construct(Deal $deal, User $user)
    {
        parent::__construct('deal_created', $deal);

        $this->deal = $deal;
        $this->entity = $deal;
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
            'total'    => $this->formatCurrency($this->deal->valued_at, $this->deal->customer),
            'customer' => $this->deal->customer->present()->name()
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
            'url'         => config('taskmanager.site_url') . 'portal/deals/' . $this->deal->id,
            'button_text' => trans('texts.view_deal'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => $this->deal->account->present()->logo(),
        ];
    }
}
