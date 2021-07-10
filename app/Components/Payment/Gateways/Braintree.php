<?php


namespace App\Components\Payment\Gateways;


use App\Models\Invoice;
use App\Models\Payment;
use Braintree\Gateway;
use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;
use Stripe\StripeClient;

class Braintree extends BasePaymentGateway
{
    private $braintree;

    /**
     * Stripe constructor.
     * @param \App\Models\Customer $customer
     * @param $customer_gateway
     * @param null $company_gateway
     */
    public function __construct(\App\Models\Customer $customer, $customer_gateway, $company_gateway = null)
    {
        parent::__construct($customer, $customer_gateway, $company_gateway);
    }

    /**
     * @param $amount
     * @param Invoice|null $invoice
     * @param bool $confirm_payment
     * @return Payment|bool|null
     */
    public function build($amount, Invoice $invoice = null, $confirm_payment = true)
    {
        $this->setupConfig();
        return $this->createCharge($amount, $invoice, $confirm_payment);
    }

    private function setupConfig()
    {
        $config = $this->company_gateway->settings;

        $this->gateway = new Gateway([
            'environment' => 'sandbox', // getConfigField('testMode')
            'merchantId'  => $config->merchant_id,
            'publicKey'   => $config->public_key,
            'privateKey'  => $config->private_key,
        ]);


        return true;
    }

    /**
     * @param float $amount
     * @param Invoice|null $invoice
     * @param bool $confirm_payment
     * @return Payment|bool|null
     */
    private function createCharge(float $amount, Invoice $invoice = null, $confirm_payment = true)
    {
        $invoice_label = $invoice !== null ? "Invoice: {$invoice->getNumber()}" : '';

        $errors = [];

        try {
            $result = $this->gateway->transaction()->sale([
                'amount'             => '10.00',
                'paymentMethodToken' => $this->customer_gateway->token,
                'options'            => [
                    'submitForSettlement' => true
                ]
            ]);

            if ($result->success) {
                // See $result->transaction for details
                $this->triggerSuccess($invoice->user, ['response' => $result->transaction->id]);
            } else {

                $error = '';

                foreach ($result->errors->deepAll() as $error) {
                    $error = $error->code . ": " . $error->message . "\n";
                }

                // Handle errors
                $this->addErrorToLog($invoice->user, ['data' => $error]);
                return false;
            }

        } catch (Exception $e) {
            $this->addErrorToLog($invoice->user, ['data' => $e->getMessage()]);
            return false;
        }

        $reference_number = $result->transaction->id;

        if (!$confirm_payment) {
            return $reference_number;
        }

        $brand = strtolower($result->transaction->creditCard['cardType']);
        $payment_method = !empty($this->card_types[$brand]) ? $this->card_types[$brand] : 12;

        if ($invoice !== null) {
            return $this->completePayment($amount, $invoice, $reference_number, $payment_method);
        }

        return true;
    }
}
