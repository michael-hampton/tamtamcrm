<?php


namespace App\Components\InvoiceCalculator;


trait Tax
{
    /**
     * @param float $unit_tax
     * @return LineItem
     * @return LineItem
     */
    public function setTaxRateEntity($name, $tax_rate): self
    {
        $this->{$name} = $tax_rate;
        return $this;
    }

    public function getTaxRateEntity($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }

        return 0;
    }
}