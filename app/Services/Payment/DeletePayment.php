<?php

namespace App\Services\Payment;

use App\Services\Transaction\TriggerTransaction;
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
        //$this->updateCustomer();
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

            $amount_due = $paymentable_credit->refunded > 0 ? $paymentable_credit->amount - $paymentable_credit->refunded : $paymentable_credit->amount;
            $credit->increaseBalance($amount_due);
            $credit->reduceAmountPaid($amount_due);
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

            $amount_due = $paymentable_invoice->refunded > 0 ? $paymentable_invoice->amount - $paymentable_invoice->refunded : $paymentable_invoice->amount;
            $invoice->resetBalance($amount_due);
            $invoice->reduceAmountPaid($amount_due);
            $invoice->customer->increaseBalance($amount_due);
            $invoice->customer->reduceAmountPaid($amount_due);
            $invoice->setStatus($delete_status);
            $invoice->save();

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
        (new TriggerTransaction($invoice))->execute(
            $invoice->total,
            $invoice->customer->balance,
            "Payment Deletion {$invoice->getNumber()}"
        );

        return true;
    }

    public function updatePayment(): bool
    {
        $this->payment->setStatus(Payment::STATUS_VOIDED);
        $this->payment->save();
        event(new PaymentWasDeleted($this->payment));

        event(new PaymentWasDeleted($this->payment));

        $this->payment->deleteEntity();

        return true;
    }

    private function updateCustomer(): bool
    {
        $customer = $this->payment->customer->fresh();

        $customer->reduceAmountPaid($this->payment->amount);

        return true;
    }
}
