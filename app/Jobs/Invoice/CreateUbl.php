<?php

namespace App\Jobs\Invoice;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use CleverIt\UBL\Invoice\Address;
use CleverIt\UBL\Invoice\Country;
use CleverIt\UBL\Invoice\Generator;
use CleverIt\UBL\Invoice\InvoiceLine;
use CleverIt\UBL\Invoice\Item;
use CleverIt\UBL\Invoice\LegalMonetaryTotal;
use CleverIt\UBL\Invoice\Party;
use CleverIt\UBL\Invoice\Price;
use CleverIt\UBL\Invoice\TaxCategory;
use CleverIt\UBL\Invoice\TaxScheme;
use CleverIt\UBL\Invoice\TaxSubTotal;
use CleverIt\UBL\Invoice\TaxTotal;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateUbl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const INVOICE_TYPE_STANDARD = 380;
    const INVOICE_TYPE_CREDIT = 381;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     *
     * @return void
     */

    public function handle()
    {
        $generator = new Generator();
        $legalMonetaryTotal = new LegalMonetaryTotal();

// adress

        $company = $this->createAccountAddress();
        $client = $this->createCustomerAddress($this->invoice->customer);


//line
        $invoiceLines = [];

        $taxable = $this->getTaxable();

        if (!empty($this->invoice->line_items)) {
            foreach ($this->invoice->line_items as $line_item) {
                $itemTaxable = $this->getItemTaxable($line_item, $taxable);

                $invoiceLines[] = $this->createInvoiceLine($line_item, $itemTaxable);
            }
        }

        // taxe TVA
        $TaxScheme = new TaxScheme();
        $TaxScheme->setId(0);
        $taxCategory = new TaxCategory();
        $taxCategory->setId(0);
        $taxCategory->setName('TVA20');
        $taxCategory->setPercent(.2);
        $taxCategory->setTaxScheme($TaxScheme);

// taxes
        $taxTotal = new TaxTotal();
        $taxSubTotal = new TaxSubTotal();
        $taxSubTotal->setTaxableAmount($this->invoice->tax_total);
        $taxSubTotal->setTaxAmount($this->invoice->tax_total);
        $taxSubTotal->setTaxCategory($taxCategory);
        $taxTotal->addTaxSubTotal($taxSubTotal);
        $taxTotal->setTaxAmount($taxSubTotal->getTaxAmount());

// invoice
        $invoice = new \CleverIt\UBL\Invoice\Invoice();
        $invoice->setId($this->invoice->number);
        $invoice->setIssueDate(date_create($this->invoice->date));
        $invoice->setInvoiceTypeCode(
            $this->invoice->total < 0 ? self::INVOICE_TYPE_CREDIT : self::INVOICE_TYPE_STANDARD
        );
        $invoice->setAccountingSupplierParty($company);
        $invoice->setAccountingCustomerParty($client);
        $invoice->setInvoiceLines($invoiceLines);

        $invoice->setLegalMonetaryTotal(
            (new LegalMonetaryTotal())
                //->setLineExtensionAmount()
                ->setTaxExclusiveAmount($taxable)
                ->setPayableAmount($this->invoice->balance)
        );


        if (!empty($this->invoice->tax_rate_name)) {
            $taxtotal = new TaxTotal();
            $taxAmount1 = 0;

            $taxAmount1 = $this->createTaxRate(
                $taxtotal,
                $taxable,
                $this->invoice->tax_rate,
                $this->invoice->tax_rate_name
            );

            $taxtotal->setTaxAmount($taxAmount1);
            $invoice->setTaxTotal($taxtotal);
        }

        // $invoice->setTaxTotal($this->invoice->tax_total);

        try {
            return $generator->invoice($invoice);
        } catch (Exception $exception) {
            info(print_r($exception, 1));
            return false;
        }
    }

    private function createAccountAddress()
    {
        $caddress = new Address();
        $caddress->setStreetName($this->invoice->account->settings->address1);
        $caddress->setCityName($this->invoice->account->settings->city);
        $caddress->setPostalZone($this->invoice->account->settings->postal_code);
        $country = new Country();

        $account_country = \App\Models\Country::where('id', $this->invoice->account->settings->country_id)->first();

        $country->setIdentificationCode($account_country->iso3);
        $caddress->setCountry($country);

// company
        $company = new Party();
        $company->setName($this->invoice->account->present()->name);
//$company->setPhysicalLocation($caddress);
        $company->setPostalAddress($caddress);

        return $company;
    }

    private
    function createCustomerAddress(
        Customer $customer
    ) {
        $customer_address = $customer->addresses->where('address_type', 1)->first();

        $client = new Party();
        $client->setName($customer->present()->name);

        if (!empty($customer_address)) {
            $caddress = new Address();
            $caddress->setStreetName($customer_address->address_1);
            $caddress->setCityName($customer_address->city);
            $caddress->setPostalZone($customer_address->zip);
            $country = new Country();

            $customer_country = \App\Models\Country::where('id', $customer_address->country_id)->first();

            $country->setIdentificationCode($customer_country->iso3);
            $caddress->setCountry($country);
            $client->setPostalAddress($caddress);
        }

        return $client;
    }

    /**
     * @return float|int|mixed
     */
    private
    function getTaxable()
    {
        $total = 0;

        if (!empty($this->invoice->line_items)) {
            foreach ($this->invoice->line_items as $item) {
                $line_total = $item->quantity * $item->unit_price; // check here

                if ($item->unit_discount != 0) {
                    if ($this->invoice->is_amount_discount) {
                        $line_total -= $item->unit_discount;
                    } else {
                        $line_total -= $line_total * $item->unit_discount / 100;
                    }
                }

                $total += $line_total;
            }
        }

        if ($this->invoice->discount_total > 0) {
            if ($this->invoice->is_amount_discount) {
                $total -= $this->invoice->discount_total;
            } else {
                $total *= (100 - $this->invoice->discount_total) / 100;
                $total = round($total, 2);
            }
        }

        if ($this->invoice->transaction_fee && $this->invoice->transaction_fee_tax) {
            $total += $this->invoice->transaction_fee;
        }


        if ($this->invoice->shipping_cost && $this->invoice->shipping_cost_tax) {
            $total += $this->invoice->shipping_cost;
        }

        return $total;
    }

    /**
     * @param $item
     * @param $invoice_total
     * @return float|int
     */
    private
    function getItemTaxable(
        $item,
        $invoice_total
    ) {
        $total = $item->quantity * $item->unit_price;

        if ($this->invoice->discount_total != 0) { // check here
            if ($this->invoice->is_amount_discount) {
                if ($invoice_total + $this->invoice->discount_total != 0) {
                    $total -= $invoice_total ? ($total / ($invoice_total + $this->invoice->discount_total) * $this->invoice->discount_total) : 0;
                }
            } else {
                $total *= (100 - $this->invoice->discount_total) / 100;
            }
        }

        if ($item->unit_discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $total -= $item->unit_discount;
            } else {
                $total -= $total * $item->unit_discount / 100;
            }
        }

        return round($total, 2);
    }

    private
    function createInvoiceLine(
        $line_item,
        $taxable
    ) {
        $product = Product::find($line_item->product_id);

        $item = new Item();
        $item->setName(!empty($product) ? $product->name : 'Untitled');

        $price = new Price();
        $price->setBaseQuantity($line_item->quantity);
        $price->setUnitCode('Unit');
        $price->setPriceAmount($line_item->unit_price);
        //$item->setDescription('');

        $invoiceLine = new InvoiceLine();
        $invoiceLine->setId(0);
        $invoiceLine->setItem($item);
        $invoiceLine->setPrice($price);
        $invoiceLine->setInvoicedQuantity($line_item->quantity);
        $invoiceLine->setLineExtensionAmount($this->costWithDiscount($line_item));

        $taxtotal = new TaxTotal();
        $itemTaxAmount1 = $this->createTaxRate($taxtotal, $taxable, $line_item->unit_tax);

        $taxtotal->setTaxAmount($itemTaxAmount1);
        $invoiceLine->setTaxTotal($taxtotal);

        return $invoiceLine;
    }

    public function costWithDiscount($item)
    {
        $cost = $item->unit_price;

        if ($item->unit_discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $cost -= $item->unit_discount / $item->quantity;
            } else {
                $cost -= $cost * $item->unit_discount / 100;
            }
        }

        return $cost;
    }

    private
    function createTaxRate(
        &$taxtotal,
        $taxable,
        $taxRate,
        $taxName = 'test'
    ) {
        $invoice = $this->invoice;
        $taxAmount = $this->taxAmount($taxable, $taxRate);
        $taxScheme = ((new TaxScheme()))->setId($taxName);

        $taxtotal->addTaxSubTotal(
            (new TaxSubTotal())
                ->setTaxAmount($taxAmount)
                ->setTaxableAmount($taxable)
                ->setTaxCategory(
                    (new TaxCategory())
                        ->setId(0)
                        ->setName($taxName)
                        ->setTaxScheme($taxScheme)
                        ->setPercent($taxRate)
                )
        );

        return $taxAmount;
    }

    private
    function taxAmount(
        $taxable,
        $rate
    ) {
        if ($this->invoice->uses_inclusive_taxes) {
            return round($taxable - ($taxable / (1 + ($rate / 100))), 2);
        } else {
            return round($taxable * ($rate / 100), 2);
        }
    }

}
