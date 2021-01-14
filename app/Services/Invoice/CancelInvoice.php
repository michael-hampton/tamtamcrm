<?php

namespace App\Services\Invoice;

use App\Events\Invoice\InvoiceWasCancelled;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Paymentable;

/**
 * Class CancelInvoice
 * @package App\Services\Invoice
 */
class CancelInvoice
{

    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var float
     */
    private float $balance;

    private bool $is_delete = false;

    /**
     * CancelInvoice constructor.
     * @param Invoice $invoice
     * @param bool $is_delete
     */
    public function __construct(Invoice $invoice, bool $is_delete = false)
    {
        $this->invoice = $invoice;
        $this->balance = $this->invoice->balance;
        $this->is_delete = $is_delete;
    }

    /**
     * @return Invoice
     */
    public function execute(): Invoice
    {
        if (!$this->is_delete && !$this->invoice->isCancellable()) {
            return $this->invoice;
        }


        $old_balance = $this->invoice->balance;

        if ($this->invoice->status_id === Invoice::STATUS_PAID) {
            $this->updatePayment();
        }

        // update customer
        $this->updateCustomer();

        // update invoice 
        if (!$this->is_delete) {
            $this->updateInvoice();
        }

        $this->invoice->transaction_service()->createTransaction(
            $old_balance,
            $this->invoice->customer->balance,
            "Invoice cancellation"
        );

        return $this->invoice;
    }

    private function updatePayment()
    {
        $paymentables = $this->invoice->paymentables()->get()->keyBy('payment_id');
        $paymentable_total = $paymentables->sum('amount');

        if ($this->balance <= 0) {
            $this->balance = $paymentable_total;
        }

        $invoice_total = $this->invoice->payments->sum('amount');

        if ((float)$paymentable_total === (float)$invoice_total) {
            Payment::whereIn('id', $this->invoice->payments->pluck('id'))->delete();
        }

        if ((float)$paymentable_total !== (float)$invoice_total) {
            $payments = $this->invoice->payments;

            foreach ($payments as $payment) {
                $amount = $paymentables[$payment->id]->amount;
                $payment->reduceAmount($amount);
            }
        }

        Paymentable::whereIn('id', $paymentables->pluck('id'))->delete();

        return true;
    }

    /**
     * @return Customer
     */
    private function updateCustomer(): Customer
    {
        $customer = $this->invoice->customer->fresh();

        if ($this->is_delete) {
            $customer->reducePaidToDateAmount($this->balance);
        }

        if ($this->invoice->status_id !== Invoice::STATUS_PAID) {
            $customer->reduceBalance($this->balance);
        }

        $customer->save();

        return $customer;
    }

    /**
     * @return Invoice
     */
    private function updateInvoice(): Invoice
    {
        $invoice = $this->invoice;

        $this->invoice->setPreviousStatus();
        $this->invoice->setPreviousBalance();
        $this->invoice->setBalance(0);
        $this->invoice->setStatus(Invoice::STATUS_CANCELLED);
        $this->invoice->setDateCancelled();
        $this->invoice->save();

        event(new InvoiceWasCancelled($invoice));

        return $this->invoice;
    }

}
