<?php

namespace App\Components\Payment\Gateways;


use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use Omnipay\Common\ItemBag;
use Omnipay\Omnipay;

class PaypalExpress extends BasePaymentGateway
{
    //https://sujipthapa.co/blog/paypal-integration-omnipay-paypal-php-library-v30-with-laravel
    //https://github.com/sudiptpa/blog-tutorials-v5.6/commit/65e4b5a6b5622654b2e48fb57360515f8ebd56be
    //cloudways.com/blog/paypal-integration-in-php/

    /**
     * PaypalExpress constructor.
     * @param Customer $customer
     * @param CompanyGateway $company_gateway
     * @param CustomerGateway|null $customer_gateway
     */
    public function __construct(
        Customer $customer,
        CompanyGateway $company_gateway,
        CustomerGateway $customer_gateway = null
    ) {
        parent::__construct($customer, $customer_gateway, $company_gateway);
        error_reporting(E_ALL & ~E_DEPRECATED);
    }

    public function capturePayment(Payment $payment)
    {
        $this->gateway();
        $ref = $payment->reference_number;

        // then later, when you want to capture it
        $data = array(
            'transactionReference' => $ref,
            'amount'               => $payment->amount // pass original amount, or can be less
        );

        $response = $this->gateway->capture($data)->send();

        if ($response->isSuccessful()) {
            // success
        } else {
            // error, maybe you took too long to capture the transaction
            $errors['data']['message'] = $response->getMessage();
            $this->addErrorToLog($payment->user, $errors);
        }
    }

    public function gateway()
    {
        $this->gateway = Omnipay::create('PayPal_Express');

        $this->gateway->initialize((array)$this->company_gateway->config);

        return $this->gateway;
    }

    /**
     * @param array $parameters
     * @param Invoice $invoice
     * @return mixed
     */
    public function purchase(array $parameters, Invoice $invoice)
    {
        $items = $this->buildItems($invoice);

        $this->gateway();

        return $this->gateway
            ->purchase($parameters)
            ->setItems($items)
            ->send();
    }

    private function buildItems(Invoice $invoice)
    {
        //https://recalll.co/?q=Omnipay%20with%20Paypal%20Express&type=code
        $items = new ItemBag();

        foreach ($invoice->line_items as $line_item) {
            if ($line_item->type_id !== Invoice::PRODUCT_TYPE) {
                continue;
            }

            $product = Product::where('id', '=', $line_item->product_id)->first();

            $items->add(
                array(
                    'name'     => $product->name,
                    'quantity' => $line_item->quantity,
                    'price'    => $product->unit_price,
                )
            );
        }
    }

    /**
     * @param array $parameters
     * @return
     */
    public function complete(array $parameters)
    {
        $this->gateway();

        $response = $this->gateway->completePurchase($parameters)
                                  ->send();

        return $response;
    }

    /**
     * @param $amount
     * @return string
     * @return string
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param $order
     * @return string
     * @return string
     */
    public function getCancelUrl($order)
    {
        return route('paypal.checkout.cancelled', $order->id);
    }

    public function authorizePayment(Invoice $invoice)
    {
        $this->gateway();

        $data = array(
            'amount'    => $invoice->total,
            'returnUrl' => 'https://www.example.com/return',
            'cancelUrl' => 'https://www.example.com/cancel',
        );

        $response = $this->gateway->authorize($data)->send();

        if ($response->isSuccessful()) {
            // success
        } else {
            // error, maybe you took too long to capture the transaction
            $errors['data']['message'] = $response->getMessage();
            $this->addErrorToLog($invoice->user, $errors);
        }
    }

    public function getReturnUrl(Invoice $invoice, Customer $customer, $amount_with_fee, $order_id = null): string
    {
        $url = $customer->account->domain() . "portal/payments/process/response";
        $url .= "?company_gateway_id={$this->company_gateway->id}&gateway_type_id=2";
        $url .= "?&amount=" . $amount_with_fee;
        $url .= "&ids=" . $invoice->id;

        if (!empty($order_id)) {
            $url .= "&order_id=" . $order_id;
        }

        // temporarily store invoice array
//        $invoice = Invoice::where('id', '=', $invoice->id)->first();
//        $invoice->temp_data = json_encode($input['invoices']);
//        $invoice->save();

        return $url;
    }

    /**
     * @param Invoice $invoice
     * @return string
     */
    public function getNotifyUrl(Invoice $invoice)
    {
        //$env = config('services.paypal.sandbox') ? "sandbox" : "live";

        $env = 'sandbox';

        return route('webhook.paypal.ipn', [$invoice->id, $env]);
    }
}
