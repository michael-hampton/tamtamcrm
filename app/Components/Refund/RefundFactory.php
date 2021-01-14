<?php


namespace App\Components\Refund;


use App\Models\Payment;
use App\Repositories\CreditRepository;

class RefundFactory
{

    /**
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     * @return Payment|bool
     */
    public function createRefund(Payment $payment, array $data, CreditRepository $credit_repo)
    {
        $objCreditRefunds = null;

        if (!empty($data['credits'])) {
            $objCreditRefunds = new CreditRefund($payment, $data, $credit_repo, $data['credits']);
            $completed_payment = $objCreditRefunds->refund();

            if (empty($data['invoices'])) {
                $this->sendRefundToGateway($completed_payment, $data, $credit_repo);
                return $completed_payment;
            }
        }

        if (!empty($data['invoices'])) {
            $completed_payment = (new InvoiceRefund($payment, $data, $credit_repo, $data['invoices']))->refund(
                $objCreditRefunds
            );
            $this->sendRefundToGateway($completed_payment, $data, $credit_repo);
            return $completed_payment;
        }

        $completed_payment = (new PaymentRefund($payment, $data, $credit_repo))->refund();

        if (!empty($data['refund_gateway'])) {
            $this->sendRefundToGateway($completed_payment, $data, $credit_repo);
        }

        return $completed_payment;
    }

    /**
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     * @return bool
     */
    private function sendRefundToGateway(Payment $payment, array $data, CreditRepository $credit_repo)
    {
        if (!empty($payment->company_gateway_id)) {
            $refund = (new GatewayRefund($payment, $data, $credit_repo))->refund();

            if (!$refund) {
                return false;
            }
        }

        return true;
    }
}
