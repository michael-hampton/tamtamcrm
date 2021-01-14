<?php

namespace App\Components\Payment\Invoice;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

/**
 * Class ReverseInvoicePayment
 * @package App\Services\Payment
 */
class ReverseInvoicePayment
{
    private Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment|null
     */
    public function execute(): ?Payment
    {
        $this->reversePayment();
        $this->updateCustomer();
        $this->payment->transaction_service()->createTransaction(
            $this->payment->amount,
            $this->payment->customer->balance
        );
        $this->payment->deletePayment();

        return $this->payment;
    }

    /**
     * @return bool
     */
    private function reversePayment(): bool
    {
        $invoices = $this->payment->invoices;

        if (empty($invoices)) {
            return true;
        }

        $delete_status = !empty(
        $this->payment->customer->getSetting(
            'invoice_payment_deleted_status'
        )
        ) ? (int)$this->payment->customer->getSetting('invoice_payment_deleted_status') : Invoice::STATUS_SENT;

        foreach ($invoices as $invoice) {
            if ($invoice->pivot->amount <= 0) {
                continue;
            }

            if ($delete_status === 100) {
                $invoice->delete();
                continue;
            }

            $invoice->setStatus($delete_status);
            $invoice->setBalance($invoice->pivot->amount);
            $invoice->save();
        }

        return true;
    }

    /**
     * @return Customer
     */
    private function updateCustomer(): Customer
    {
        $customer = $this->payment->customer;
        $customer->increaseBalance($this->payment->amount);
        $customer->reducePaidToDateAmount($this->payment->amount);
        $customer->save();
        return $customer;
    }
}
