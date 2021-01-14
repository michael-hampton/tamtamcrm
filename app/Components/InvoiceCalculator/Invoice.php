<?php

namespace App\Components\InvoiceCalculator;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class Invoice
 * @package App\Components\InvoiceCalculator
 */
class Invoice extends BaseCalculator
{
    /**
     * @var float
     */
    private $discount_total = 0.00;

    /**
     * @var float
     */
    private $sub_total = 0.00;

    /**
     * @var float
     */
    private $balance = 0.00;

    /**
     * @var float
     */
    private $tax_total = 0.00;

    /**
     * @var float
     */
    private $total = 0.00;

    /**
     * @var float
     */
    private $custom_tax = 0.00;

    /**
     * @var float
     */
    private $discount_percentage;

    /**
     * @var float
     */
    private $tax_rate;

    /**
     * @var float
     */
    private $tax_2;

    /**
     * @var float
     */
    private $tax_3;

    /**
     * @var float
     */
    private $taxable_amount;

    /**
     * @var array
     */
    private $line_items = [];

    private $partial = 0;

    /**
     * @var bool
     */
    private $inclusive_taxes = false;
    /**
     * @var bool
     */
    private $is_amount_discount = false;

    /**
     * InvoiceCalculator constructor.
     * @param $entity
     */
    public function __construct($entity)
    {
        parent::__construct($entity);

        $this->entity = $entity;
    }

    public function build()
    {
        $this->total = $this->sub_total;
        $this->calculateCustomValues();
        $this->calculateDiscount();
        $this->calculateTax();

        $this->getCalculatedBalance();
    }

    private function calculateCustomValues()
    {
        $custom_surcharge_total = 0;

        if (!empty($this->entity->transaction_fee)) {
            $custom_surcharge_total += $this->entity->transaction_fee;

//            if (!empty($this->entity->custom_surcharge_tax1)) {
//                $tax_total = $this->applyTax($this->entity->transaction_fee_tax, $this->sub_total, true);
//                $this->setTaxTotal($tax_total);
//                $this->setCustomTax($this->entity->transaction_fee);
//            }
        }

        if (!empty($this->entity->shipping_cost)) {
            $custom_surcharge_total += $this->entity->shipping_cost;

            if (!empty($this->entity->shipping_cost_tax)) {
                $tax_total = $this->applyTax(
                    $this->entity->shipping_cost_tax,
                    $this->sub_total,
                    $this->is_amount_discount
                );
                $this->setTaxTotal($tax_total);
                $this->setCustomTax($this->entity->shipping_cost);
            }
        }

        if ($custom_surcharge_total > 0) {
            $this->total += $custom_surcharge_total;
        }

        if (!empty($this->entity->gateway_fee) && $this->entity->gateway_fee > 0) {
            $this->applyGatewayFee();
        }

        return $this;
    }

    /**
     * @param float $custom_tax
     * @return Invoice
     * @return Invoice
     */
    public function setCustomTax(float $custom_tax): self
    {
        $this->custom_tax += $custom_tax;
        return $this;
    }

    private function applyGatewayFee(): ?bool
    {
        if (\App\Models\Invoice::class !== get_class(
                $this->entity
            ) || empty($this->entity->gateway_fee) || $this->entity->gateway_fee_applied) {
            return true;
        }

        $is_percentage = !empty($this->entity->gateway_percentage) && ($this->entity->gateway_percentage === 'true' || $this->entity->gateway_percentage === true);
        $gateway_fee = $this->calculateGatewayFee($this->total, $this->entity->gateway_fee, $is_percentage);
        $this->entity->gateway_fee = $gateway_fee;
        $this->entity->gateway_fee_applied = true;

        if (get_class(
                $this->entity
            ) === 'App\Models\Invoice' && !empty($this->entity->account->settings->charge_gateway_to_customer) && $this->entity->account->settings->charge_gateway_to_customer === true) {
            $this->addChargeToLineItems(
                $gateway_fee,
                trans('texts.gateway_fee'),
                \App\Models\Invoice::GATEWAY_FEE_TYPE
            );
        }

        return true;
    }

    /**
     * @param $charge
     * @param $description
     * @param $type_id
     * @return LineItem
     */
    private function addChargeToLineItems($charge, $description, $type_id): LineItem
    {
        $line_item = (new LineItem);

        $line_item->setQuantity(1)
                  ->setDescription($description)
                  ->setUnitPrice($charge)
                  ->setTypeId($type_id)
//                ->setProductId($description)
                  ->setNotes($description)
                  ->setSubTotal($charge);

        $this->addItem($line_item->toObject(), true);

        $this->entity->updateCustomerBalance($charge);

        return $line_item;
    }

    /**
     * @param $item
     * @param bool $is_charge
     * @return $this
     */
    public function addItem($item, bool $is_charge = false)
    {
        $this->setTaxTotal($item->tax_total);
        $this->setDiscountTotal($item->discount_total);
        $this->setSubTotal($item->line_total);
        $this->line_items[] = $item;

        if ($is_charge) {
            $this->increaseBalance($item->line_total);
            $this->increaseTotal($item->line_total);
        }

        return $this;
    }

    /**
     * @param float $balance
     * @return $this
     */
    private function increaseBalance(float $balance): self
    {
        $this->balance += $balance;
        return $this;
    }

    /**
     * @param float $total
     * @return $this
     */
    private function increaseTotal(float $total): self
    {
        $this->total += $total;
        return $this;
    }

    /**
     * @return Invoice
     */
    public function calculateDiscount(): self
    {
        $this->total -= $this->discount_total;

        return $this;
    }

    /**
     * @return $this
     */
    public function calculateTax(): self
    {
        $sub_total = $this->custom_tax > 0 ? $this->total + $this->custom_tax : $this->total;
        $this->tax_total += $this->applyTax($sub_total, $this->tax_rate, $this->is_amount_discount);

        if ($this->tax_2 && $this->tax_2 > 0) {
            $this->tax_total += $this->applyTax($sub_total, $this->tax_2, $this->is_amount_discount);
        }
        if ($this->tax_3 && $this->tax_3 > 0) {
            $this->tax_total += $this->applyTax($sub_total, $this->tax_3, $this->is_amount_discount);
        }

        if ($this->custom_tax > 0) {
            $this->total = $sub_total;
        }

        $this->total += $this->tax_total;

        return $this;
    }

    /**
     * @return false|float
     */
    public function getCalculatedBalance()
    {
        $balance = !$this->entity->id ? $this->total : $this->getBalance();
        $this->balance = $this->calculateBalance($this->total, $balance);

        return $this->balance;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     * @return Invoice
     * @return Invoice
     */
    public function setBalance($balance): self
    {
        if (empty($balance)) {
            return $this;
        }

        $this->balance = $balance;
        return $this;
    }

    /**
     * @param float $late_fee_charge
     * @return \App\Models\Invoice|null
     */
    public function addLateFeeToInvoice(float $late_fee_charge): ?\App\Models\Invoice
    {
        if (empty($late_fee_charge) || $late_fee_charge <= 0 || get_class($this->entity) !== 'App\Models\Invoice') {
            return null;
        }

        $this->addChargeToLineItems(
            $late_fee_charge,
            'Late Fee Charge applied',
            $this->entity::LATE_FEE_TYPE
        );

        return $this->rebuildEntity();
    }

    public function rebuildEntity()
    {
        Log::emergency($this->getSubTotal() . ' ' . $this->getTotal());

        $this->entity->sub_total = $this->getSubTotal();
        $this->entity->balance = $this->getBalance();
        $this->entity->total = $this->getTotal();
        $this->entity->tax_total = $this->getTaxTotal();
        $this->entity->discount_total = $this->getDiscountTotal();
        $this->entity->partial = $this->getPartial();
        $this->entity->line_items = $this->getLineItems();
        return $this->entity;
    }

    /**
     * @return float
     */
    public function getSubTotal(): float
    {
        return $this->sub_total;
    }

    /**
     * @param float $sub_total
     * @return $this
     */
    public function setSubTotal(float $sub_total): self
    {
        $this->sub_total += $sub_total;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     * @return $this
     */
    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxTotal(): float
    {
        return $this->tax_total;
    }

    /**
     * @param float $tax_total
     * @return Invoice
     * @return Invoice
     */
    public function setTaxTotal(float $tax_total): self
    {
        $this->tax_total += $tax_total;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountTotal(): float
    {
        return $this->discount_total;
    }

    /**
     * @param float $discount_total
     * @return $this
     */
    public function setDiscountTotal(float $discount_total): self
    {
        if (!empty($this->discount_total)) {
            return $this;
        }

        $this->discount_total += $discount_total;
        return $this;
    }

    /**
     * @return int
     */
    public function getPartial(): int
    {
        return $this->partial;
    }

    /**
     * @param int $partial
     * @return Invoice
     * @return Invoice
     */
    public function setPartial($partial): self
    {
        $this->partial =
            max(0, min(round($partial, 2), $this->entity->balance));
        return $this;
    }

    /**
     * @return array|Collection
     */
    public function getLineItems()
    {
        return $this->line_items;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->tax_rate;
    }

    /**
     * @param $name
     * @param float $tax_rate
     * @return Invoice
     */
    public function setTaxRate($name, $tax_rate): self
    {
        $this->{$name} = $tax_rate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInclusiveTaxes(): bool
    {
        return $this->inclusive_taxes;
    }

    /**
     * @param bool $inclusive_taxes
     * @return Invoice
     * @return Invoice
     */
    public function setInclusiveTaxes(bool $inclusive_taxes): self
    {
        $this->inclusive_taxes = $inclusive_taxes;
        return $this;
    }
}
