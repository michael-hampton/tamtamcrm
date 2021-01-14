<?php

namespace App\Components\Payment;

use App\Events\Payment\PaymentWasDeleted;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Payment;

class DeletePayment
{
    /**
     * @var Payment
     */
    private Payment $payment;

    private $paymentables;

    /**
     * DeletePayment constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->paymentables = $payment->paymentables;
    }

    public function execute()
    {
        $this->updateCredit();
        $this->updateInvoice();
        $this->updateCustomer();
        $this->updatePayment();

        return $this->payment;
    }

    /**
     * @return bool
     */
    private function updateCredit(): bool
    {
        $paymentable_credits = $this->paymentables->where('paymentable_type', Credit::class)->keyBy('paymentable_id');

        $credits = Credit::whereIn('id', $paymentable_credits->pluck('paymentable_id'))->get()->keyBy('id');

        if ($paymentable_credits->count() === 0 || $credits->count() === 0) {
            return true;
        }

        $delete_status = !empty(
        $this->payment->customer->getSetting(
            'invoice_payment_deleted_status'
        )
        ) ? (int)$this->payment->customer->getSetting('credit_payment_deleted_status') : Credit::STATUS_SENT;

        foreach ($paymentable_credits as $id => $paymentable_credit) {
            $credit = $credits[$id];

            $paymentable_credit->delete();

            if ($delete_status === 100) {
                $credit->delete();
                continue;
            }

            $credit->increaseBalance($paymentable_credit->amount);
            $credit->setStatus($delete_status);
            $credit->save();
        }

        return true;
    }

    private function updateInvoice(): bool
    {
        $paymentable_invoices = $this->paymentables->where('paymentable_type', Invoice::class)->keyBy('paymentable_id');

        $invoices = Invoice::whereIn('id', $paymentable_invoices->pluck('paymentable_id'))->get()->keyBy('id');

        if ($paymentable_invoices->count() === 0 || $invoices->count() === 0) {
            return true;
        }

        $delete_status = !empty(
        $this->payment->customer->getSetting(
            'invoice_payment_deleted_status'
        )
        ) ? (int)$this->payment->customer->getSetting('invoice_payment_deleted_status') : Invoice::STATUS_SENT;

        foreach ($paymentable_invoices as $id => $paymentable_invoice) {
            $invoice = $invoices[$id];

            $paymentable_invoice->delete();

            if ($delete_status === 100) {
                $invoice->delete();
                continue;
            }

            $invoice->resetBalance($paymentable_invoice->amount);
            $invoice->customer->increaseBalance($paymentable_invoice->amount);
            $invoice->setStatus($delete_status);

            // create transaction
            $this->createTransaction($invoice);
        }

        return true;
    }

    /**
     * @param Invoice $invoice
     * @return bool
     */
    private function createTransaction(Invoice $invoice): bool
    {
        $invoice->transaction_service()->createTransaction(
            $invoice->total,
            $invoice->customer->balance,
            'Payment Deletion'
        );

        return true;
    }

    private function updateCustomer(): bool
    {
        $customer = $this->payment->customer;
        $customer->reducePaidToDateAmount($this->payment->amount);

        return true;
    }

    public function updatePayment(): bool
    {
        $this->payment->setStatus(Payment::STATUS_VOIDED);
        $this->payment->save();
        event(new PaymentWasDeleted($this->payment));

        event(new PaymentWasDeleted($this->payment));

        $this->payment->delete();

        return true;
    }
}
