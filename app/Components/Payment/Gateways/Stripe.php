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
        $config = $this->company_gateway->settings;

        $this->stripe = new StripeClient(
            $config->apiKey
        );

        return true;
    }

    public function connectAccount($account)
    {
        $this->setupConfig();

        $response = $this->stripe->accountLinks->create([
            'account'     => 'acct_1032D82eZvKYlo2C',
            'refresh_url' => 'http://taskman2.develop/stripe/reauth',
            'return_url'  => 'https://taskman2.develop/stripe/return',
            'type'        => 'account_onboarding',
        ]);

        echo '<pre>';
        print_r($response);
        die;

    }

    public function retrieveBankAccount($account_number, $bank_account_number)
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->retrieveExternalAccount(
            $account_number,
            $bank_account_number,
            []
        );

        echo '<pre>';
        print_r($response);
        die;
    }

    public function attachBankAccount($account)
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->createExternalAccount(
            $account,
            [
                'external_account' => [
                    'object'              => 'bank_account',
                    'country'             => 'GB',
                    'currency'            => 'GBP',
                    'account_holder_name' => 'Michael Hampton',
                    'account_holder_type' => 'individual',
                    'routing_number'      => '108800',
                    'account_number'      => '00012345'
                ],
            ]
        );

        echo $response['id'];

        echo '<pre>';
        print_r($response);
        die;
    }

    public function retrieveAccount($account)
    {
        $this->setupConfig();

        $stripe_account = $this->stripe->accounts->retrieve(
            $account,
            []
        );

        echo '<pre>';
        print_r($stripe_account);
        die;
    }

    public function createAccount()
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->create([
            'type'         => 'express',
            'country'      => 'GB',
            'email'        => 'michaelhamptondesign@yahoo.com',
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers'     => ['requested' => true],
            ],
        ]);

        echo $response['id'];

        echo '<pre>';
        print_r($response);
        die;
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

            $this->triggerSuccess($invoice->user, ['response' => $response]);
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
            echo $e->getMessage();
            die;
            // Something else happened, completely unrelated to Stripe
            $errors['error_status'] = $e->getHttpStatus();
            $errors['error_type'] = $e->getError()->type;
            $errors['error_code'] = $e->getError()->code;
            // param is '' in this case
            $errors['param'] = $e->getError()->param;
            $errors['error_message'] = 'unexpected error';
        }

        if (!empty($errors)) {
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

                $this->triggerSuccess($payment->user, ['response' => $response]);

                return $payment->fresh();
            }

            return $this->stripe->charges->capture(
                $payment->reference_number,
                []
            );
        } catch (Exception $e) {
            $errors['data']['error_message'] = $e->getMessage();
            $this->addErrorToLog($payment->user, $errors);
        }

        return null;
    }
}
