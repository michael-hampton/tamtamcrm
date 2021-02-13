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

        $applying_existing_payment = $this->applyToExistingPayment($payment, $data);

        if(!empty($payment->id)) {
            $data['amount'] = $payment->amount;
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

    private function applyToExistingPayment(Payment $payment, array $data)
    {
        if (!empty($payment->amount) && $payment->paymentables->count(
            ) === 0 && (!empty($data['credits']) || !empty($data['invoices']))) {
            //applying payment - keep original amount
            return true;
        }

        if($payment->applied > 0 && $payment->applied < $payment->amount) {
            return true;
        }

        return false;
    }
}
