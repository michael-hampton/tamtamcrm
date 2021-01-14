<?php


namespace App\Components\Payment\Gateways;


use App\Components\InvoiceCalculator\GatewayCalculator;
use App\Models\CompanyGateway;
use App\Models\CustomerGateway;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;

class CalculateGatewayFee
{
    /**
     * @var Invoice
     */
    private Invoice $invoice;

    /**
     * @var InvoiceRepository
     */
    private InvoiceRepository $invoice_repo;

    /**
     * @var CustomerGateway
     */
    private ?CustomerGateway $customer_gateway;

    /**
     * CalculateGatewayFee constructor.
     * @param Invoice $invoice
     * @param InvoiceRepository $invoice_repo
     */
    public function __construct(Invoice $invoice, InvoiceRepository $invoice_repo)
    {
        $this->invoice = $invoice;
        $this->invoice_repo = $invoice_repo;
    }

    /**
     * @param bool $add_fee_to_invoice
     * @return bool|float
     */
    public function getFee(bool $add_fee_to_invoice = false)
    {
        $amount = $this->invoice->partial > 0 ? $this->invoice->partial : $this->invoice->balance;

        $this->customer_gateway = $this->findGatewayFee($amount, $add_fee_to_invoice);

        if (empty($this->customer_gateway)) {
            return false;
        }

        $company_gateway = $this->customer_gateway->company_gateway;

        $amount = $this->calculateFee($company_gateway, $amount, $add_fee_to_invoice);

        return $amount;
    }

    private function findGatewayFee($amount, $add_fee_to_invoice): ?CustomerGateway
    {
        $gateways = $this->invoice->customer->gateways()->orderBy('is_default', 'DESC')->get();

        foreach ($gateways as $gateway) {
            $company_gateway = $gateway->company_gateway;

            if (empty($company_gateway->fees_and_limits)) {
                continue;
            }

            $fees_and_limits = $company_gateway->fees_and_limits[0];

            if ((!empty($fees_and_limits->min_limit) && $amount < $fees_and_limits->min_limit) || (!empty($fees_and_limits->max_limit) && $amount > $fees_and_limits->max_limit)) {
                continue;
            }

            return $gateway;
        }

        return null;
    }

    /**
     * @param CompanyGateway $company_gateway
     * @param float $amount
     * @param bool $add_fee_to_invoice
     * @return float
     */
    private function calculateFee(CompanyGateway $company_gateway, float $amount, bool $add_fee_to_invoice)
    {
        $fee = $company_gateway->fees_and_limits[0];

        $fee = (new GatewayCalculator($company_gateway))
            ->setSubTotal($amount)
            ->setFeeAmount($fee->fee_amount)
            ->setFeePercent($fee->fee_percent)
            ->setTaxRate('tax_rate', !empty($fee->tax) ? $fee->tax : 0)
            ->setTaxRate('tax_2', !empty($fee->tax_2) ? $fee->tax_2 : 0)
            ->setTaxRate('tax_3', !empty($fee->tax_3) ? $fee->tax_3 : 0)
            ->build()
            ->getFeeTotal();

        if (!empty($fee)) {
            $amount += $fee;
            $this->addFeeToInvoice($fee, $add_fee_to_invoice);
        }

        return $amount;
    }

    private function addFeeToInvoice($fee, bool $add_fee_to_invoice)
    {
        if (empty($fee) || !$add_fee_to_invoice) {
            return true;
        }

        $this->invoice_repo->save(['gateway_fee' => $fee], $this->invoice);

        return $fee;
    }

    /**
     * @return CustomerGateway|null
     */
    public function getCustomerGateway(): ?CustomerGateway
    {
        return $this->customer_gateway;
    }

}