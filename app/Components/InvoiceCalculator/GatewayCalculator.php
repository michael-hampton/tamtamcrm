<?php

namespace App\Components\InvoiceCalculator;

/**
 * Class LineItem
 * @package App\Components\InvoiceCalculator
 */
class GatewayCalculator extends BaseCalculator
{
    /**
     * @var float
     */
    private $min_limit = 0.00;

    /**
     * @var float
     */
    private $sub_total = 0.00;

    /**
     * @var float
     */
    private $total = 0.00;

    /**
     * @var float
     */
    private float $max_limit = 0.00;

    /**
     * @var int
     */
    private $fee_amount = 0;

    /**
     * @var float
     */
    private $fee_percent = 0.00;

    /**
     * @var float
     */
    private $tax_total = 0.00;

    /**
     * @var float
     */
    private $fee_total = 0.00;

    /**
     * @var float
     */
    private $fee_cap = 0;

    /**
     * GatewayCalculator constructor.
     * @param $entity
     */
    public function __construct($entity = null)
    {
        parent::__construct($entity);
    }

    public function build()
    {
        $this->calculateAmount();
        $this->calculateTax();
        return $this;
    }

    public function calculateAmount(): self
    {
        $this->fee_total = 0;

        $this->fee_total += $this->fee_amount;

        if ($this->fee_percent > 0) {
            $this->fee_total += $this->applyDiscount($this->sub_total, $this->fee_percent, false);
        }

        if ($this->fee_cap > 0 && $this->fee_total > $this->fee_cap) {
            $this->fee_total = $this->fee_cap;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function calculateTax(): self
    {
        $this->tax_total += $this->applyTax($this->fee_total, $this->tax_rate);

        if ($this->tax_2 && $this->tax_2 > 0) {
            $this->tax_total += $this->applyTax($this->fee_total, $this->tax_2);
        }
        if ($this->tax_3 && $this->tax_3 > 0) {
            $this->tax_total += $this->applyTax($this->fee_total, $this->tax_3);
        }

        $this->fee_total += $this->tax_total;

        return $this;
    }


    public function toObject()
    {
        return (object)[
            'tax_rate'        => $this->getTaxRate('tax_rate'),
            'tax_2'           => $this->getTaxRate('tax_rate'),
            'tax_3'           => $this->getTaxRate('tax_rate'),
            'min_value'       => $this->getMinLimit(),
            'max_value'       => $this->getMaxLimit(),
            'fee_amount'      => $this->getFeeAmount(),
            'fee_percent'     => $this->getFeePercent(),
            'tax_rate_name'   => $this->getTaxRateName('tax_rate_name'),
            'tax_rate_name_2' => $this->getTaxRateName('tax_rate_name_2'),
            'tax_rate_name_3' => $this->getTaxRateName('tax_rate_name_3')
            //'tax_total'          => $this->gett()
        ];
    }

    /**
     * @param $name
     * @return float
     */
    public function getTaxRate($name): float
    {
        return $this->{$name};
    }

    /**
     * @return float
     */
    public function getMinLimit(): float
    {
        return $this->min_limit;
    }

    /**
     * @param float $min_limit
     * @return $this
     */
    public function setMinLimit(float $min_limit): self
    {
        $this->min_limit = (float)$min_limit;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxLimit(): float
    {
        return $this->max_limit;
    }

    /**
     * @param float $max_limit
     * @return $this
     */
    public function setMaxLimit(float $max_limit): self
    {
        $this->max_limit = (float)$max_limit;
        return $this;
    }

    /**
     * @return float
     */
    public function getFeeAmount(): float
    {
        return $this->fee_amount;
    }

    /**
     * @param float $fee_amount
     * @return $this
     */
    public function setFeeAmount(float $fee_amount): self
    {
        $this->fee_amount = (float)$fee_amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getFeePercent(): float
    {
        return $this->fee_percent;
    }

    public function setFeePercent(float $fee_percent): self
    {
        $this->fee_percent = (float)$fee_percent;
        return $this;
    }

    /**
     * @param $name
     * @return string
     */
    public function getTaxRateName($name): string
    {
        return $this->{$name};
    }

    /**
     * @param $name
     * @param float $tax_rate
     * @return GatewayCalculator
     */
    public function setTaxRate($name, $tax_rate): self
    {
        $this->{$name} = $tax_rate;
        return $this;
    }

    public function setTaxRateName($name, string $value): self
    {
        $this->{$name} = (string)$value;
        return $this;
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
        $this->sub_total = $sub_total;
        return $this;
    }

    /**
     * @return float
     */
    public function getFeeTotal(): float
    {
        return $this->fee_total;
    }

    /**
     * @return int|null
     */
    public function getTaxRateId(): ?int
    {
        return $this->tax_rate_id;
    }

    /**
     * @return float
     */
    public function getFeeCap(): float
    {
        return $this->fee_cap;
    }

    /**
     * @param float $fee_cap
     */
    public function setFeeCap(float $fee_cap): void
    {
        $this->fee_cap = $fee_cap;
    }
}
