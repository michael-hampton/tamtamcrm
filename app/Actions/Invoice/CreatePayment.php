<?php

namespace App\Actions\Invoice;

use App\Actions\BaseActions;
use App\Actions\Transaction\TriggerTransaction;
use App\Events\Invoice\InvoiceWasPaid;
use App\Factory\InvoiceToPaymentFactory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;

class CreatePayment extends BaseActions
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
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * CreatePayment constructor.
     * @param Invoice $invoice
     * @param InvoiceRepository $invoice_repo
     * @param PaymentRepository $payment_repo
     */
    public function __construct(Invoice $invoice, InvoiceRepository $invoice_repo, PaymentRepository $payment_repo)
    {
        $this->invoice = $invoice;
        $this->payment_repo = $payment_repo;
        $this->invoice_repo = $invoice_repo;
    }

    public function execute()
    {
        if ($this->invoice->balance < 0 || $this->invoice->status_id == Invoice::STATUS_PAID || $this->invoice->hide === true) {
            return false;
        }

        // create payment
        $payment = $this->createPayment();

        // update invoice 
        $this->updateInvoice($payment);

        // update customer
        $this->updateCustomer($payment);

        $payment = $this->invoice->payments->first();

        event(new InvoiceWasPaid($this->invoice, $payment));

        $this->sendPaymentEmail($this->invoice, $this->invoice_repo);

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

        (new TriggerTransaction($payment))->execute(
            $payment->amount * -1,
            $customer->balance,
            "Update customer balance {$this->invoice->getNumber()}"
        );

        return $customer;
    }

}
