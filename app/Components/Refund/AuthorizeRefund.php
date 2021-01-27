<?php


namespace App\Components\Refund;


use App\Models\CompanyGateway;
use App\Models\Payment;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerProfilePaymentType;
use net\authorize\api\contract\v1\GetTransactionDetailsRequest;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\PaymentProfileType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;
use net\authorize\api\controller\GetTransactionDetailsController;

class AuthorizeRefund extends BaseRefund
{
    /**
     * @var array
     */
    private array $data;

    /**
     * AuthorizeRefund constructor.
     * @param Payment $payment
     * @param CompanyGateway $company_gateway
     * @param array $data
     */
    public function __construct(Payment $payment, CompanyGateway $company_gateway, array $data)
    {
        $this->payment = $payment;
        $this->company_gateway = $company_gateway;
        $this->data = $data;
    }

    public function build()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $this->setupConfig();

        $transaction_details = $this->getTransactionDetails($this->payment->reference_number);

        $profile = $transaction_details->getProfile();

        if (!empty($profile)) {
            $this->buildPaymentProfile($transaction_details->getProfile()->getCustomerPaymentProfileId());
            $this->buildCustomerProfile($transaction_details->getProfile()->getCustomerProfileId());
        }

        $this->createCreditCard($transaction_details->getPayment()->getCreditCard());
        $this->createTransaction();
        $response = $this->sendRequest();

        return $response;
    }

    private function setupConfig()
    {
        $config = $this->company_gateway->settings;

        /* Create a merchantAuthenticationType object with authentication details
        retrieved from the constants file */
        $this->config = new MerchantAuthenticationType();
        $this->config->setName($config->apiLoginId);
        $this->config->setTransactionKey($config->transactionKey);

        return true;
    }

    private function getTransactionDetails($transactionId)
    {
        // Set the transaction's refId
        // The refId is a Merchant-assigned reference ID for the request.
        // If included in the request, this value is included in the response.
        // This feature might be especially useful for multi-threaded applications.
        $refId = 'ref' . time();

        $request = new GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($this->config);
        $request->setTransId($transactionId);

        $controller = new GetTransactionDetailsController($request);

        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return $response->getTransaction();
        }

        $errorMessages = $response->getMessages()->getMessage();
        $this->addErrorToLog(
            $this->payment->user,
            ['error' => $errorMessages[0]->getText(), 'code' => $errorMessages[0]->getCode()]
        );
    }

    private function buildPaymentProfile($customer_payment_profile_id)
    {
        // set payment profile for customer

        $this->paymentProfile = new PaymentProfileType();

        $this->paymentProfile->setpaymentProfileId($customer_payment_profile_id);
    }

    private function buildCustomerProfile($customer_profile_id)
    {
        // set customer profile

        $this->customerProfile = new CustomerProfilePaymentType();

        $this->customerProfile->setCustomerProfileId($customer_profile_id);

        $this->customerProfile->setPaymentProfile($this->paymentProfile);
    }

    private function createCreditCard($credit_card_details)
    {
        // Create the payment data for a credit card
        $creditCard = new CreditCardType();
        $creditCard->setCardNumber($credit_card_details->getCardNumber());
        $creditCard->setExpirationDate("XXXX");
        $this->payment_data = new PaymentType();
        $this->payment_data->setCreditCard($creditCard);

        return true;
    }

    private function createTransaction()
    {
        $amount = $this->data['amount'] ?? $this->payment->amount;
        $this->transactionRequest = new TransactionRequestType();
        $this->transactionRequest->setTransactionType("refundTransaction");
        $this->transactionRequest->setAmount(round($amount, 2));

        if (isset($this->customerProfile)) {
            $this->transactionRequest->setProfile($this->customerProfile);
        } else {
            $this->transactionRequest->setPayment($this->payment_data);
        }

        $this->transactionRequest->setRefTransId($this->payment->reference_number);

        return true;
    }

    private function sendRequest()
    {
        //https://developer.authorize.net/api/reference/index.html#payment-transactions-refund-a-transaction

        // Set the transaction's refId
        $refId = 'ref' . time();

        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($this->config);
        $request->setRefId($refId);
        $request->setTransactionRequest($this->transactionRequest);
        $controller = new CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $this->triggerSuccess(
                        $this->payment->user,
                        [
                            'response_code'  => $tresponse->getResponseCode(),
                            'transaction_id' => $tresponse->getTransId(),
                            'code'           => $tresponse->getMessages()[0]->getCode(),
                            'description'    => $tresponse->getMessages()[0]->getDescription()
                        ]
                    );

                    return true;
                }

                if ($tresponse->getErrors() != null) {
                    $errors = [
                        'code'    => $tresponse->getErrors()[0]->getErrorCode(),
                        'message' => $tresponse->getErrors()[0]->getErrorText()
                    ];
                }

                $this->addErrorToLog($this->payment->user, $errors);
                return false;
            }

            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getErrors() != null) {
                $errors = [
                    'code'    => $tresponse->getErrors()[0]->getErrorCode(),
                    'message' => $tresponse->getErrors()[0]->getErrorText()
                ];

                $this->addErrorToLog($this->payment->user, $errors);
                return false;
            }


            $errors = [
                'code'    => $response->getMessages()->getMessage()[0]->getCode(),
                'message' => $response->getMessages()->getMessage()[0]->getText()
            ];

            $this->addErrorToLog($this->payment->user, $errors);
            return false;
        }

        return false;
    }
}