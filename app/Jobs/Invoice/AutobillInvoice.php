<?php

namespace App\Jobs\Invoice;

use App\Components\Payment\Gateways\CalculateGatewayFee;
use App\Components\Payment\Gateways\GatewayFactory;
use App\Jobs\Payment\CreatePayment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Traits\CreditPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutobillInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreditPayment;

    private Invoice $invoice;

    private InvoiceRepository $invoice_repo;

    public function __construct(Invoice $invoice, InvoiceRepository $invoice_repo)
    {
        $this->invoice = $invoice;
        $this->invoice_repo = $invoice_repo;
    }

    public function handle()
    {
        if ($this->invoice->is_deleted || !in_array(
                $this->invoice->status_id,
                [
                    Invoice::STATUS_SENT,
                    Invoice::STATUS_PARTIAL,
                    Invoice::STATUS_DRAFT
                ]
            )) {
            return null;
        }

        return $this->build();
    }

    private function build($return_fee = false)
    {
        if ($this->invoice->balance <= 0 && $this->invoice->partial <= 0 && $this->invoice->customer->getSetting(
                'credit_payments_enabled'
            ) === true) {
            return $this->completePaymentWithCredit();
        }

        $objCalculateGatewayFee = new CalculateGatewayFee($this->invoice, $this->invoice_repo);
        $amount = $objCalculateGatewayFee->getFee(true);
        $customer_gateway = $objCalculateGatewayFee->getCustomerGateway();

        if (empty($customer_gateway)) {
            return false;
        }

        $company_gateway = $customer_gateway->company_gateway;

        $gateway_obj = (new GatewayFactory($customer_gateway, $company_gateway))->create(
            $this->invoice->customer->fresh()
        );
        return $gateway_obj->build($amount, $this->invoice);
    }

    /**
     * @return Payment|null
     */
    private function completePaymentWithCredit(): ?Payment
    {
        $data = [
            'payment_method'     => null,
            'payment_type'       => PaymentMethod::CREDIT,
            'amount'             => $this->invoice->balance,
            'customer_id'        => $this->invoice->customer->id,
            'company_gateway_id' => null,
            'ids'                => $this->invoice->id,
            'order_id'           => null,
            'apply_credits'      => true
        ];

        $payment = CreatePayment::dispatchNow($data, (new PaymentRepository(new Payment())));

        return $payment;
    }
}
