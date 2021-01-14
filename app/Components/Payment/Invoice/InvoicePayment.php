<?php

namespace App\Components\Payment\Invoice;

use App\Components\Payment\BasePaymentProcessor;
use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

class InvoicePayment extends BasePaymentProcessor
{
    /**
     * @var array|mixed
     */
    private array $invoices;

    /**
     * InvoicePayment constructor.
     * @param array $data
     * @param Payment $payment
     * @param PaymentRepository $payment_repo
     */
    public function __construct(array $data, Payment $payment, PaymentRepository $payment_repo)
    {
        parent::__construct($payment, $payment_repo, $data);
        $this->invoices = $data['invoices'];
    }

    /**
     * @param null $objCreditPayment
     * @return Payment
     */
    public function process($objCreditPayment = null)
    {
        $invoices = Invoice::whereIn('id', array_column($this->invoices, 'invoice_id'))->get();
        $payment_invoices = collect($this->invoices)->keyBy('invoice_id')->toArray();

        foreach ($invoices as $invoice) {
            if (empty($payment_invoices[$invoice->id])) {
                continue;
            }

            $amount = $payment_invoices[$invoice->id]['amount'];

            if ($invoice->gateway_fee > 0) {
                $this->setGatewayFee($invoice->gateway_fee);
            }

            $this->payment->attachInvoice($invoice, $amount);

            $this->increasePaymentAmount($amount);

            (new RecalculateInvoice($invoice, $this->payment->fresh(), $amount))->execute();
        }

        $this->reduceCreditedAmount($objCreditPayment);

        $this->save();

        return $this->payment;
    }

    /**
     * @param null $objCreditPayment
     * @return bool
     */
    private function reduceCreditedAmount($objCreditPayment = null)
    {
        if ($objCreditPayment === null || $objCreditPayment->getAmount() <= 0) {
            return true;
        }

        $this->setCreditedAmount($objCreditPayment->getAmount());
        $this->reducePaymentAmount($objCreditPayment->getAmount());
        return true;
    }
}
