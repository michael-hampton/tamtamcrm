<?php


namespace App\Components\Payment\Gateways;


use App\Models\Invoice;
use App\Models\Payment;
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

class Stripe extends BasePaymentGateway
{
    private $stripe;

    /**
     * Stripe constructor.
     * @param \App\Models\Customer $customer
     * @param $customer_gateway
     * @param $company_gateway
     */
    public function __construct(\App\Models\Customer $customer, $customer_gateway, $company_gateway)
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
        $config = $this->company_gateway->config;

        $this->stripe = new StripeClient(
            $config->apiKey
        );

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
        $currency = $this->customer->currency;
        $credit_card = $this->findCreditCard();

        if (empty($credit_card)) {
            return false;
        }

        $invoice_label = $invoice !== null ? "Invoice: {$invoice->getNumber()}" : '';

        //https://stripe.com/docs/api/errors/handling
        $errors = [];

        try {
            $response = $this->stripe->paymentIntents->create(
                [
                    'payment_method' => $this->customer_gateway->token,
                    'customer'       => $this->customer_gateway->gateway_customer_reference,
                    'confirm'        => true,
                    'capture_method' => !$confirm_payment ? 'manual' : 'automatic',
                    'amount'         => $this->convertToStripeAmount(round($amount, 2), $currency->precision),
                    'currency'       => $currency->iso_code,
                    'description'    => "{$invoice_label} Amount: {$amount} Customer: {$this->customer->name}",
                ]
            );
        } catch (CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = $e->getError();
        } catch (RateLimitException $e) {
            // Too many requests made to the API too quickly
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;

            $errors['error_message'] = 'Too many requests made to the API too quickly';
            return false;
        } catch (InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'Invalid parameters were supplied to Stripes API';
        } catch (AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'Authentication with Stripes API failed';
        } catch (ApiConnectionException $e) {
            // Network communication with Stripe failed
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'Network communication with Stripe failed';
        } catch (ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'unexpected error';
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'unexpected error';
        }

        if (!empty($errors)) {
            echo '<pre>';
            print_r($errors);

            $user = !empty($invoice) ? $invoice->user : $this->customer->user;
            $this->addErrorToLog($user, $errors);
            Log::emergency($errors);
            return false;
        }

        $reference_number = !$confirm_payment ? $response->id : $response->charges->data[0]->id;

        if (!$confirm_payment) {
            return $reference_number;
        }

        $brand = $response->charges->data[0]->payment_method_details->card->brand;
        $payment_method = !empty($this->card_types[$brand]) ? $this->card_types[$brand] : 12;

        if ($invoice !== null) {
            return $this->completePayment($amount, $invoice, $reference_number, $payment_method);
        }

        return true;
    }

    private function findCreditCard()
    {
        $stripe_customer = $this->getStripeCustomer();

        $payment_methods = array_filter(
            (array)$stripe_customer->sources['data'],
            function ($var) {
                return ($var->object == 'card');
            }
        );

        if (empty($payment_methods)) {
            return false;
        }

        $payment_method = array_values($payment_methods)[0];

        return $payment_method;
    }

    private function getStripeCustomer(): Customer
    {
        return $this->stripe->customers->retrieve($this->customer_gateway->gateway_customer_reference);
    }

    private function convertToStripeAmount($amount, $precision)
    {
        return $amount * pow(10, $precision);
    }

    /**
     * @param Payment $payment
     * @param bool $payment_intent
     * @return Payment|null
     */
    public function capturePayment(Payment $payment, $payment_intent = true): ?Payment
    {
        $this->setupConfig();

        //https://stripe.com/docs/api/errors/handling
        $errors = [];

        try {
            if ($payment_intent) {
                $response = $this->stripe->paymentIntents->capture(
                    $payment->reference_number,
                    []
                );

                $ref = $response->charges->data[0]->id;
                $payment->reference_number = $ref;
                $payment->save();

                return $payment->fresh();
            }

            return $this->stripe->charges->capture(
                $payment->reference_number,
                []
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
            $errors['data']['error_message'] = $e->getMessage();
            $this->addErrorToLog($payment->user, $errors);
        }

        return null;
    }
}
