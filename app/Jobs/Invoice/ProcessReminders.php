<?php

namespace App\Jobs\Invoice;

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

        foreach ($invoices as $invoice) {
            $this->build($invoice);
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     */
    private function build(Invoice $invoice)
    {
        $message_sent = false;

        for ($counter = 1; $counter <= 3; $counter++) {
            $reminder_date = $invoice->date_to_send;

            if ($invoice->customer->getSetting(
                    "reminder{$counter}_enabled"
                ) === false || $invoice->customer->getSetting(
                    "number_of_days_after_{$counter}"
                ) == 0 || !$reminder_date->isToday()) {
                continue;
            }

            if (!$message_sent) {
                $this->addCharge($invoice, $counter);

                $this->sendEmail($invoice, "reminder{$counter}");

                $this->updateNextReminderDate(
                    $invoice,
                    $invoice->customer->getSetting("scheduled_to_send_{$counter}"),
                    $invoice->customer->getSetting("number_of_days_after_{$counter}")
                );
                $message_sent = true;
            }
        }
    }

    /**
     * @param Invoice $invoice
     * @param $counter
     * @return bool
     */
    private function addCharge(Invoice $invoice, $counter): bool
    {
        $amount = $this->calculateAmount($invoice, $counter);

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
    private function calculateAmount(Invoice $invoice, $counter)
    {
        $percentage = $invoice->customer->getSetting("percent_to_charge_{$counter}");
        $current_total = $invoice->partial > 0 ? $invoice->partial : $invoice->balance;

        if (!empty($percentage)) {
            return round(($percentage / 100) * $current_total, 2);
        }

        $amount = $invoice->customer->getSetting("amount_to_charge_{$counter}");

        if (empty($amount)) {
            return null;
        }

        return $amount;
    }

    /**
     * @param Invoice $invoice
     * @param $template
     */
    private function sendEmail(Invoice $invoice, $template)
    {
        $subject = $invoice->customer->getSetting($template . '_subject');
        $body = $invoice->customer->getSetting($template . '_message');
        $invoice->service()->sendEmail(null, $subject, $body, $template);
    }

    /**
     * @param Invoice $invoice
     * @param $reminder_type
     * @param $number_of_days
     * @return bool
     */
    private function updateNextReminderDate(Invoice $invoice, $reminder_type, $number_of_days)
    {
        $date = $reminder_type === 'after_invoice_date' ? $invoice->date : $invoice->due_date;
        $next_send_date = $reminder_type === 'before_due_date' ? Carbon::parse($date)->subDays($number_of_days)->format(
            'Y-m-d'
        ) : Carbon::parse($date)->addDays($number_of_days)->format('Y-m-d');

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
