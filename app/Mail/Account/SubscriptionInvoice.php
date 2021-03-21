<?php

namespace App\Mail\Account;

use App\Models\Account;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlanSubscription;
use App\Traits\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionInvoice extends Mailable
{
    use Queueable, SerializesModels, Money;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var Invoice
     */
    private Invoice $invoice;

    private PlanSubscription $plan;

    private $message;

    /**
     * @var array
     */
    private array $message_array;

    /**
     * Create a new message instance.
     *
     * @param Account $account
     * @param Invoice $invoice
     */
    public function __construct(PlanSubscription $plan, Account $account, Invoice $invoice)
    {
        $this->account = $account;
        $this->invoice = $invoice;
        $this->plan = $plan;
    }

    public function build()
    {
        $this->setSubject();
        $this->setMessage();
        $this->buildMessage();

        return $this->to($this->account->support_email)
                    ->from('tamtamcrm@support.com')
                    ->subject($this->subject)
                    ->markdown(
                        'email.admin.new',
                        [
                            'data' => $this->message_array
                        ]
                    );
    }

    private function setSubject()
    {
        $this->subject = trans('texts.subscription_invoice_created_subject');
    }

    private function setMessage()
    {
        $this->message = trans('texts.subscription_invoice_created_message', $this->getDataArray());
    }

    private function getDataArray()
    {

        $cost = $this->plan->plan->price * $this->plan->number_of_licences;

        return [
            'term'     => $this->plan->plan->invoice_interval === 'year' ? 'Yearly' : 'Monthly',
            'number'   => $this->invoice->getNumber(),
            'due_date' => date('d-m-Y', strtotime($this->plan->due_date)),
            'amount'   => self::formatCurrency($cost, $this->invoice->customer)
        ];
    }

    private function buildMessage()
    {
        $this->message_array = [
            'title'       => $this->subject,
            'message'     => $this->message,
            'url'         => config('taskmanager.site_url') . '/invoices/' . $this->invoice->id,
            'button_text' => trans('texts.view_invoice'),
            //'signature'   => isset($this->invoice->account->settings->email_signature) ? $this->order->account->settings->email_signature : '',
            'logo'        => $this->invoice->account->present()->logo(),
        ];
    }
}
