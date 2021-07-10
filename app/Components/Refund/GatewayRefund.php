<?php


namespace App\Components\Refund;


use App\Models\CompanyGateway;
use App\Models\Payment;
use App\Repositories\CreditRepository;
use Braintree\Gateway;
use Omnipay\Omnipay;
use Stripe\StripeClient;

class GatewayRefund extends BaseRefund
{
    const AUTHORIZE_ID = '8ab2dce2';
    const STRIPE_ID = '13bb8d58';
    const PAYPAL_ID = '64bcbdce';
    const BRAINTREE_ID = 'dlmqa4gvpy';

    /**
     * GatewayRefund constructor.
     * @param Payment $payment
     * @param array $data
     * @param CreditRepository $credit_repo
     */
    public function __construct(Payment $payment, array $data, CreditRepository $credit_repo)
    {
        parent::__construct($payment, $data, $credit_repo);
        $this->payment = $payment;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function refund()
    {
        if (empty($this->payment->company_gateway_id)) {
            return false;
        }

        $company_gateway = CompanyGateway::find($this->payment->company_gateway_id);

        if (!$company_gateway) {
            return false;
        }

        if ($company_gateway->gateway->key === self::AUTHORIZE_ID) {
            return (new AuthorizeRefund($this->payment, $company_gateway, $this->data))->build();
        }

        if ($company_gateway->gateway->key === self::STRIPE_ID) {
            return $this->doStripeRefund($company_gateway);
        }

        if ($company_gateway->gateway->key === self::PAYPAL_ID) {
            return $this->doPaypalRefund($company_gateway);
        }

        if ($company_gateway->gateway->key === self::BRAINTREE_ID) {
            return $this->doBraintreeRefund($company_gateway);
        }

        return false;
    }

    /**
     * @param CompanyGateway $company_gateway
     * @return bool
     */
    private function doStripeRefund(CompanyGateway $company_gateway): bool
    {
        //https://stripe.com/docs/api/refunds/object

        try {
            $stripe = new StripeClient(
                $company_gateway->config->apiKey
            );

            $response = $stripe->refunds->create(
                [
                    'charge' => $this->payment->reference_number,
                ]
            );

            if ($response->status == $response::STATUS_SUCCEEDED) {
                $this->payment->reference_number = $response->charge;
                $this->payment->save();

                $this->triggerSuccess($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' =>'Payment has been refunded'
                    ]
                );

                return true;
            } else {
                $this->addErrorToLog($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' => 'failed to do stripe refund'
                    ]
                );
            }
        } catch (\Exception $exception) {
            $this->addErrorToLog($this->payment->user,
                [
                    'company_gateway_id' => $company_gateway->id,
                    'reference_number' => $this->payment->reference_number,
                    'number' =>  $this->payment->number,
                    'message' => $exception->getMessage()
                ]
            );
        }

        return false;
    }

    /**
     * @param CompanyGateway $company_gateway
     * @return bool
     */
    private function doPaypalRefund(CompanyGateway $company_gateway): bool
    {
        try {
            $gateway = Omnipay::create($company_gateway->gateway->provider);

            $gateway->initialize((array)$company_gateway->settings);

            $response = $gateway
                ->refund(
                    [
                        'transactionReference' => $this->payment->reference_number,
                        'amount'               => $this->data['amount'] ?? $this->payment->amount,
                        'currency'             => $this->payment->customer->currency->code
                    ]
                )
                ->send();

            if ($response->isSuccessful()) {
                $this->triggerSuccess($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' =>'Payment has been refunded'
                    ]
                );
                return true;
            } else {
                $this->addErrorToLog($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' => 'failed to do paypal refund'
                    ]
                );
            }
        } catch (\Exception $exception) {
            $this->addErrorToLog($this->payment->user,
                [
                    'company_gateway_id' => $company_gateway->id,
                    'reference_number' => $this->payment->reference_number,
                    'number' =>  $this->payment->number,
                    'message' => $exception->getMessage()
                ]
            );
        }


        return false;
    }

    /**
     * @param CompanyGateway $company_gateway
     * @return bool
     */
    private function doBraintreeRefund(CompanyGateway $company_gateway): bool
    {
        $config = $company_gateway->settings;

        try {
            $gateway = new Gateway([
                'environment' => 'sandbox',
                'merchantId'  => $config->merchant_id,
                'publicKey'   => $config->public_key,
                'privateKey'  => $config->private_key,
            ]);

            $result = $gateway->transaction()->refund($this->payment->reference_number);

            if ($result->success) {
                $this->triggerSuccess($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' =>'Payment has been refunded'
                    ]
                );

                return true;
            } else {
                $this->addErrorToLog($this->payment->user,
                    [
                        'company_gateway_id' => $company_gateway->id,
                        'reference_number' => $this->payment->reference_number,
                        'number' =>  $this->payment->number,
                        'message' => 'failed to do braintree refund'
                    ]
                );
            }
        } catch (\Exception $exception) {
            $this->addErrorToLog($this->payment->user,
                [
                    'company_gateway_id' => $company_gateway->id,
                    'reference_number' => $this->payment->reference_number,
                    'number' =>  $this->payment->number,
                    'message' => $exception->getMessage()
                ]
            );
        }

        return false;
    }

}