<?php

namespace App\Mail\Admin;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class QuoteRejected extends AdminMailer
{
    use Queueable, SerializesModels;

    /**
     * @var Quote
     */
    private Quote $quote;

    /**
     * QuoteApproved constructor.
     * @param Quote $quote
     * @param User $user
     */
    public function __construct(Quote $quote, User $user)
    {
        parent::__construct('quote_rejected', $quote);

        $this->quote = $quote;
        $this->entity = $quote;
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
            'total' => $this->quote->getFormattedTotal(),
            'quote' => $this->quote->getNumber(),
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
            'url'         => $this->getUrl() . 'quotes/' . $this->quote->id,
            'button_text' => trans('texts.view_quote'),
            'signature'   => !empty($this->settings) ? $this->settings->email_signature : '',
            'logo'        => $this->quote->account->present()->logo(),
        ];
    }
}
