<?php


namespace App\Components\Payment;


use App\Components\Payment\Invoice\InvoicePayment;
use App\Models\Payment;
use App\Repositories\PaymentRepository;

class ProcessPayment
{

    /**
     * @param array $data
     * @param PaymentRepository $payment_repo
     * @param Payment $payment
     * @return Payment|null
     */
    public function process(array $data, PaymentRepository $payment_repo, Payment $payment): ?Payment
    {
        $payment = $payment_repo->save($data, $payment);

        $objCreditPayment = null;

        if (!empty($data['credits'])) {
            $objCreditPayment = new CreditPayment($data, $payment, $payment_repo);
            $payment = $objCreditPayment->process();
        }

        if (!empty($data['invoices'])) {
            $payment = (new InvoicePayment($data, $payment, $payment_repo))->process($objCreditPayment);
        }

        return $payment->fresh();
    }
}
