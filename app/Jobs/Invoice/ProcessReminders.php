<?php

namespace App\Jobs\Invoice;

use App\Models\Reminders;
use App\Services\Email\DispatchEmail;
use App\Components\InvoiceCalculator\InvoiceCalculator;
use App\Jobs\Subscription\SendSubscription;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;

class ProcessReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * ProcessReminders constructor.
     * @param InvoiceRepository $invoice_repo
     */
    public function __construct(InvoiceRepository $invoice_repo)
    {
        $this->invoice_repo = $invoice_repo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processReminders();
        $this->processLateInvoices();
    }

    private function processReminders()
    {
        $invoices = $this->invoice_repo->getInvoiceReminders();
        $reminders = Reminders::query()->where('enabled', true)->get()->groupBy('account_id');

        foreach ($invoices as $invoice) {
            $this->build($invoice, $reminders);
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     */
    private function build(Invoice $invoice, $reminders)
    {
        $message_sent = false;

        if (!empty($invoice->late_fee_reminder) && $invoice->late_fee_reminder > 0) {
            $reminder = Reminders::whereId($invoice->late_fee_reminder)->first();
            $this->processReminder($invoice, $reminder);
            return true;
        }

        if (empty($reminders[$invoice->account_id])) {
            return false;
        }

        foreach ($reminders[$invoice->account_id] as $reminder) {
            $reminder_date = $invoice->date_to_send;

            if(empty($reminder['number_of_days_after']) || !$reminder_date->isToday()) {
                continue;
            }

            if (!$message_sent) {
                $this->processReminder($invoice, $reminder);
                $message_sent = true;
            }
        }
    }

    private function processReminder(Invoice $invoice, Reminders $reminder)
    {
        $this->addCharge($invoice, $reminder);

        $this->sendEmail($invoice, $reminder);

        $this->updateNextReminderDate($invoice, $reminder);

        return true;
    }

    /**
     * @param Invoice $invoice
     * @param $counter
     * @return bool
     */
    private function addCharge(Invoice $invoice, Reminders $reminder): bool
    {
        $amount = $this->calculateAmount($invoice, $reminder);

        if (empty($amount)) {
            return true;
        }

        $invoice->late_fee_charge += $amount;

        $objInvoice = (new InvoiceCalculator($invoice))->build();
        $invoice = $objInvoice->addLateFeeToInvoice($amount);

        if (empty($invoice)) {
            return false;
        }

        $invoice->save();

        return true;
    }

    /**
     * @param Invoice $invoice
     * @param $counter
     * @return false|float|null
     */
    private function calculateAmount(Invoice $invoice, Reminders $reminder)
    {
        $current_total = $invoice->partial > 0 ? $invoice->partial : $invoice->balance;
        $amount = $reminder->amount_to_charge;

        return $reminder->amount_type === 'percent' ? round(($amount / 100) * $current_total, 2) : $amount;
    }

    /**
     * @param Invoice $invoice
     * @param $template
     */
    private function sendEmail(Invoice $invoice, Reminders  $reminder)
    {
        $subject = $reminder->subject;
        $body = $reminder->message;

        (new DispatchEmail($invoice))->execute(null, $subject, $body, '');
    }

    /**
     * @param Invoice $invoice
     * @param $reminder_type
     * @param $number_of_days
     * @return bool
     */
    private function updateNextReminderDate(Invoice $invoice, Reminders $reminder)
    {
        $date = $reminder->scheduled_to_send === 'after_invoice_date' ? $invoice->date : $invoice->due_date;

        $next_send_date = $reminder->scheduled_to_send === 'before_due_date' ? Carbon::parse($date)->subDays($reminder->number_of_days_after)->format(
            'Y-m-d'
        ) : Carbon::parse($date)->addDays($reminder->number_of_days_after)->format('Y-m-d');

        $invoice->date_to_send = $next_send_date;
        $invoice->date_reminder_last_sent = Carbon::now();
        $invoice->save();
        return true;
    }

    private function processLateInvoices()
    {
        $invoices = $this->invoice_repo->getExpiredInvoices();

        foreach ($invoices as $invoice) {
            $this->handleLateInvoices($invoice);
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     */
    private function handleLateInvoices(Invoice $invoice)
    {
        $event_name = 'LATEINVOICES';
        $class = new ReflectionClass(Subscription::class);
        $value = $class->getConstant(strtoupper($event_name));

        SendSubscription::dispatchNow($invoice, $value);
    }
}
