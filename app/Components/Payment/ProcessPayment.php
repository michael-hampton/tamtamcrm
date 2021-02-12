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
        if (empty($data['invoices']) && empty($data['credits'])) {
            $data['status_id'] = Payment::STATUS_PENDING;
        }

        $applying_existing_payment = false;

        if (!empty($payment->amount) && $payment->paymentables->count(
            ) === 0 && (!empty($data['credits']) || !empty($data['invoices']))) {
            //applying payment - keep original amount
            $data['amount'] = $payment->amount;
            $applying_existing_payment = true;
        }

        $payment = $payment_repo->save($data, $payment);

        $objCreditPayment = null;

        if (!empty($data['credits'])) {
            $objCreditPayment = new CreditPayment($data, $payment, $payment_repo, $applying_existing_payment);
            $payment = $objCreditPayment->process();
        }

        if (!empty($data['invoices'])) {
            $payment = (new InvoicePayment($data, $payment, $payment_repo, $applying_existing_payment))->process(
                $objCreditPayment
            );
        }

        return $payment->fresh();
    }
}
