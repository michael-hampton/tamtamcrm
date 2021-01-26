<?php

namespace App\Services\Invoice;

use App\Factory\InvoiceToPaymentFactory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

class CreatePayment
{
    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var PaymentRepository
     */
    private PaymentRepository $payment_repo;

    /**
     * CreatePayment constructor.
     * @param Invoice $invoice
     * @param PaymentRepository $payment_repo
     */
    public function __construct(Invoice $invoice, PaymentRepository $payment_repo)
    {
        $this->invoice = $invoice;
        $this->payment_repo = $payment_repo;
    }

    public function execute()
    {
        if ($this->invoice->balance < 0 || $this->invoice->status_id == Invoice::STATUS_PAID || $this->invoice->is_deleted === true) {
            return false;
        }

        // create payment
        $payment = $this->createPayment();

        // update invoice 
        $this->updateInvoice($payment);

        // update customer
        $this->updateCustomer($payment);

        return $this->invoice;
    }

    /**
     * @return Payment
     */
    private function createPayment(): Payment
    {
        $payment = $this->payment_repo->save(
            [
                'reference_number' => trans('texts.manual')
            ],
            InvoiceToPaymentFactory::create($this->invoice)
        );

        // attach invoices to payment
        $payment = $payment->attachInvoice($this->invoice, $payment->amount, true);

        return $payment;
    }

    /**
     * @param Payment $payment
     * @return Invoice
     */
    private function updateInvoice(Payment $payment): Invoice
    {
        $this->invoice->reduceBalance($payment->amount);
        $this->invoice->increaseAmountPaid($payment->amount);
        $this->invoice->setStatus(Invoice::STATUS_PAID);
        $this->invoice->save();
        return $this->invoice;
    }

    /**
     * @param Payment $payment
     * @return Customer
     */
    private function updateCustomer(Payment $payment): Customer
    {
        $customer = $this->invoice->customer->fresh();
        $customer->reduceBalance($payment->amount);
        $customer->increaseAmountPaid($payment->amount);
        $customer->save();

        $payment->transaction_service()->createTransaction(
            $payment->amount * -1,
            $customer->balance,
            "Update customer balance {$this->invoice->getNumber()}"
        );

        return $customer;
    }

}
