<?php

namespace App\Services\Invoice;

use App\Components\InvoiceCalculator\LineItem;
use App\Events\Invoice\InvoiceWasReversed;
use App\Factory\CreditFactory;
use App\Models\Credit;
use App\Models\Invoice;
use App\Repositories\CreditRepository;
use App\Repositories\PaymentRepository;
use ReflectionException;

/**
 * Class ReverseInvoicePayment
 * @package App\Services\Invoice
 */
class ReverseInvoicePayment
{

    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var CreditRepository
     */
    private CreditRepository $credit_repo;

    /**
     * @var PaymentRepository
     */
    private PaymentRepository $payment_repo;

    /**
     * @var float|mixed
     */
    private float $balance;

    /**
     * @var string
     */
    private string $note;

    /**
     * HandleReversal constructor.
     * @param Invoice $invoice
     * @param CreditRepository $credit_repo
     * @param PaymentRepository $payment_repo
     */
    public function __construct(Invoice $invoice, CreditRepository $credit_repo, PaymentRepository $payment_repo)
    {
        $this->credit_repo = $credit_repo;
        $this->payment_repo = $payment_repo;
        $this->invoice = $invoice;
    }

    public function execute()
    {
        if (!$this->invoice->isReversable()) {
            return $this->invoice;
        }

        $this->setBalance();

        $this->setNote();

        $total_paid = $this->invoice->reversePaymentsForInvoice();

        // create Credit note
        if ($total_paid > 0) {
            $credit_note = $this->createCreditNote($total_paid);
        }

        // update customer
        $this->updateCustomer($total_paid);

        $this->createTransaction();

        // update invoice
        $this->updateInvoice();

        return $this->invoice;
    }

    private function setBalance()
    {
        $this->balance = $this->invoice->balance;
    }

    private function setNote()
    {
        $this->note = "Credit for reversal of " . $this->invoice->getNumber();
    }

    /**
     * @param float $total_paid
     * @return Credit|null
     * @throws ReflectionException
     */
    private function createCreditNote(float $total_paid)
    {
        $credit = CreditFactory::create($this->invoice->account, $this->invoice->user, $this->invoice->customer);
        $credit->setInvoiceId($this->invoice);

        $line_items[] = (new LineItem)
            ->setQuantity(1)
            ->setUnitPrice($total_paid)
            ->setNotes($this->note)
            ->toObject();

        $credit = $this->credit_repo->save(['line_items' => $line_items], $credit);

        $this->credit_repo->markSent($credit);

        if ($this->invoice->payments()->count() > 0) {
            $payment = $this->invoice->payments->first();

            $payment->attachCredit($credit, $credit->total);
            $credit->reversePaymentsForCredit($total_paid);
        }

        return $credit;
    }

    /**
     * @param float $total_paid
     * @return bool
     */
    private function updateCustomer(float $total_paid): bool
    {
        $customer = $this->invoice->customer;
        $customer->reduceBalance($this->balance);
        $customer->reducePaidToDateAmount($total_paid);
        $customer->save();

        return true;
    }

    private function createTransaction()
    {
        $this->invoice->transaction_service()->createTransaction(
            $this->balance * -1,
            $this->invoice->customer->balance,
            $this->note
        );
    }

    /**
     * @return bool
     */
    private function updateInvoice(): bool
    {
        $invoice = $this->invoice;
        $this->invoice->setPreviousStatus();
        $this->invoice->setPreviousBalance();
        $this->invoice->setBalance(0);
        $this->invoice->setStatus(Invoice::STATUS_REVERSED);
        $this->invoice->save();

        event(new InvoiceWasReversed($invoice));

        return true;
    }
}
