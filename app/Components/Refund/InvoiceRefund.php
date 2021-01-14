<?php

namespace App\Components\Refund;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\CreditRepository;

class InvoiceRefund extends BaseRefund
{
    private array $payment_invoices;

    /**
     * InvoiceRefund constructor.
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repository
     * @param array $payment_invoices
     */
    public function __construct(
        Payment $payment,
        array $data,
        CreditRepository $credit_repository,
        array $payment_invoices
    ) {
        parent::__construct($payment, $data, $credit_repository);
        $this->payment_invoices = $payment_invoices;
    }

    /**
     * @param CreditRefund|null $objCreditRefund
     * @return Payment
     */
    public function refund(CreditRefund $objCreditRefund = null)
    {
        $ids = array_column($this->payment_invoices, 'invoice_id');
        $invoices = Invoice::whereIn('id', $ids)->get()->keyBy('id');

        foreach ($this->payment_invoices as $payment_invoice) {
            if (!isset($invoices[$payment_invoice['invoice_id']])) {
                continue;
            }

            $invoice = $invoices[$payment_invoice['invoice_id']];

            if (!$this->updateRefundedAmountForInvoice($invoice, $payment_invoice['amount'])) {
                continue;
            }

            $this->createLineItem($payment_invoice['amount'], $invoice);
            $this->increaseRefundAmount($payment_invoice['amount']);
            $invoice->resetBalance($payment_invoice['amount']);
        }

        $this->reduceCreditedAmount($objCreditRefund);
        $this->save();

        return $this->payment;
    }

    /**
     * @param Invoice $invoice
     * @param $amount
     * @return bool
     */
    private function updateRefundedAmountForInvoice(Invoice $invoice, $amount): bool
    {
        $paymentable_invoice = $invoice->paymentables()->where('payment_id', '=', $this->payment->id)->first();

        if (($amount + $paymentable_invoice->refunded) > $invoice->total) {
            return false;
        }

        $paymentable_invoice->refunded += $amount;
        $paymentable_invoice->save();
        return true;
    }

    /**
     * @param CreditRefund|null $objCreditRefund
     * @return bool
     */
    private function reduceCreditedAmount(CreditRefund $objCreditRefund = null)
    {
        if ($objCreditRefund === null || $objCreditRefund->getAmount() <= 0) {
            return true;
        }

        $this->reduceRefundAmount($objCreditRefund->getAmount());
        return true;
    }
}
