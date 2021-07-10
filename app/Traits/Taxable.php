<?php

namespace App\Traits;

use App\Models\Invoice;

trait Taxable
{

    private array $map = [];

    public function getTaxes(int $precision)
    {
        $taxable = $this->calculateTaxes($this->uses_inclusive_taxes, $precision);
        $paidAmount = $this->total - $this->balance;

        if ($this->tax_rate > 0) {
            $invoiceTaxAmount = $taxable[$this->tax_rate_name];
            $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
                ? round(($paidAmount / $this->total * $invoiceTaxAmount), $precision)
                : 0.0;
            $this->calculateTax(
                $this->tax_rate_name,
                $this->tax_rate,
                $invoiceTaxAmount,
                $invoicePaidAmount
            );
        }

        if ($this->tax_2 > 0) {
            $invoiceTaxAmount = $taxable[$this->tax_rate_name_2];
            $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
                ? round(($paidAmount / $this->total * $invoiceTaxAmount), $precision)
                : 0.0;
            $this->calculateTax(
                $this->tax_rate_name_2,
                $this->tax_rate,
                $invoiceTaxAmount,
                $invoicePaidAmount
            );
        }

        if ($this->tax_3 > 0) {
            $invoiceTaxAmount = $taxable[$this->tax_rate_name_3];
            $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
                ? round(($paidAmount / $this->total * $invoiceTaxAmount), $precision)
                : 0.0;
            $this->calculateTax(
                $this->tax_rate_name_3,
                $this->tax_rate,
                $invoiceTaxAmount,
                $invoicePaidAmount
            );
        }

        foreach ($this->line_items as $item) {
            if ($item->unit_tax > 0) {
                $itemTaxAmount = $taxable[$item->tax_rate_name];
                $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
                    ? round(($paidAmount / $this->total * $itemTaxAmount), $precision)
                    : 0.0;
                $this->calculateTax(
                    $item->tax_rate_name,
                    $item->unit_tax,
                    $itemTaxAmount,
                    $itemPaidAmount
                );
            }

            if (!empty($item->tax_2)) {
                $itemTaxAmount = $taxable[$item->tax_rate_name_2];
                $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
                    ? round(($paidAmount / $this->total * $itemTaxAmount), $precision)
                    : 0.0;
                $this->calculateTax(
                    $item->tax_rate_name_2,
                    $item->tax_2,
                    $itemTaxAmount,
                    $itemPaidAmount
                );
            }

            if (!empty($item->tax_3)) {
                $itemTaxAmount = $taxable[$item->tax_rate_name_3];
                $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
                    ? round(($paidAmount / $this->total * $itemTaxAmount), $precision)
                    : 0.0;
                $this->calculateTax(
                    $item->tax_rate_name_3,
                    $item->tax_3,
                    $itemTaxAmount,
                    $itemPaidAmount
                );
            }
        }

        return $this->map;
    }

    private function calculateTaxes()
    {
        $total = $this->calculateSubtotal();

        $taxAmount = 0;
        $map = [];

        foreach ($this->line_items as $item) {
            $lineTotal = $this->getItemTaxable($item, $total);

            if ($item->unit_tax > 0) {
                $taxAmount = $this->calculateTaxAmount(
                    $lineTotal,
                    $item->unit_tax,
                    $this->uses_inclusive_taxes
                );

                $map[$item->tax_rate_name] = !isset($map[$item->tax_rate_name]) ? $taxAmount : $map[$item->tax_rate_name] += $taxAmount;
            }

            if (!empty($item->tax_2)) {
                $taxAmount = $this->calculateTaxAmount(
                    $lineTotal,
                    $item->tax_2,
                    $this->uses_inclusive_taxes
                );
                $map[$item->tax_rate_name_2] = !isset($map[$item->tax_rate_name_2]) ? $taxAmount : $map[$item->tax_rate_name_2] += $taxAmount;
            }

            if (!empty($item->tax_3)) {
                $taxAmount = $this->calculateTaxAmount(
                    $lineTotal,
                    $item->tax_3,
                    $this->uses_inclusive_taxes
                );
                $map[$item->tax_rate_name_3] = !isset($map[$item->tax_rate_name_3]) ? $taxAmount : $map[$item->tax_rate_name_3] += $taxAmount;
            }
        }

        if ($this->discount_total > 0) {
            if ($this->is_amount_discount) {
                $total -= $this->discount_total;
            } else {
                $total -= $total * $this->discount_total / 100;
            }
        }

        if ($this->shipping_cost > 0 && $this->shipping_cost_tax > 0) {
            $total += $this->shipping_cost;
        }

        if ($this->transaction_fee > 0 && $this->transaction_fee_tax > 0) {
            $total += $this->transaction_fee;
        }

        if ($this->tax_rate > 0) {
            $taxAmount =
                $this->calculateTaxAmount($total, $this->tax_rate, $this->uses_inclusive_taxes);
            $map[$this->tax_rate_name] = !isset($map[$this->tax_rate_name]) ? $taxAmount : $map[$this->tax_rate_name] += $taxAmount;
        }

        if ($this->tax_2 > 0) {
            $taxAmount =
                $this->calculateTaxAmount($total, $this->tax_2, $this->uses_inclusive_taxes);
            $map[$this->tax_rate_name_2] = !isset($map[$this->tax_rate_name_2]) ? $taxAmount : $map[$this->tax_rate_name_2] += $taxAmount;
        }

        if ($this->tax_3 > 0) {
            $taxAmount =
                $this->calculateTaxAmount($total, $this->tax_3, $this->uses_inclusive_taxes);
            $map[$this->tax_rate_name_3] = !isset($map[$this->tax_rate_name_3]) ? $taxAmount : $map[$this > tax_rate_name_3] += $taxAmount;
        }

        return $map;
    }

    private function calculateSubtotal()
    {
        $total = 0.0;

        foreach ($this->line_items as $item) {
            $lineTotal = $item->quantity * $item->unit_price;

            if ($item->unit_discount > 0) {
                if ($this->is_amount_discount) {
                    $lineTotal -= $item->unit_discount;
                } else {
                    $lineTotal -= $lineTotal * $item->unit_discount / 100;
                }
            }

            $total += $lineTotal;
        }

        return $total;
    }

    private function getItemTaxable($item, float $invoiceTotal)
    {
        $lineTotal = $item->quantity * $item->unit_price;

        if ($this->discount_total > 0) {
            if ($this->is_amount_discount) {
                if ($invoiceTotal > 0) {
                    $lineTotal -= round($lineTotal / $invoiceTotal * $this->discount_total, 2);
                }
            }
        }

        if ($item->unit_discount > 0) {
            if ($this->is_amount_discount) {
                $lineTotal -= $item->unit_discount;
            } else {
                $lineTotal -= round($lineTotal * $item->unit_discount / 100, 2);
            }
        }

        return $lineTotal;
    }

    private function calculateTaxAmount(float $amount, float $rate)
    {
        $taxAmount = 0;

        if ($this->uses_inclusive_taxes) {
            return $amount - ($amount / (1 + ($rate / 100)));
        }

        return round($amount * $rate / 100, 2);
    }

    protected function calculateLineItemTaxAmount($invoice, object $line_item, float $rate, int $precision)
    {
        if (empty($rate)) {
            return 0;
        }

        $line_total = $this->calculateLineItemTotal($invoice, $line_item);
        $tax_amount = $invoice->is_amount_discount ? $line_total - ($line_total / (1 + ($rate / 100))) : $line_total * $rate / 100;

        return round($tax_amount, $precision);
    }

    private function calculateTax(string $name, float $rate, float $amount, float $paid)
    {
        if (empty($amount)) {
            return false;
        }

        $key = $rate . ' ' . $name;

        if (!isset($this->map[$key])) {
            $this->map[$key] = [
                'name'   => $name,
                'rate'   => $rate,
                'amount' => 0,
                'paid'   => 0
            ];
        }

        $this->map[$key]['amount'] += $amount;
        $this->map[$key]['paid'] += $paid;
    }

    protected function calculateLineItemTotal($invoice, $line_item)
    {
        $total = $line_item->quantity * $line_item->unit_price;

        if (!empty($line_item->unit_discount)) {
            if ($invoice->is_amount_discount) {
                $total = $total - $line_item->unit_discount;
            } else {
                $total = $total - ($line_item->unit_discount / 100 * $total);
            }
        }

        return round($total, 2);
    }

    protected function getLineItemTaxTotal($invoice, object $line_item, int $precision): float
    {
        $tax_amount = 0;

        if (!empty($line_item->unit_tax)) {
            $tax_amount += $this->calculateLineItemTaxAmount($invoice, $line_item, $line_item->unit_tax, 2);
        }

        if (!empty($line_item->tax_2)) {
            $tax_amount += $this->calculateLineItemTaxAmount($invoice, $line_item, $line_item->tax_2, 2);
        }

        if (!empty($line_item->tax_3)) {
            $tax_amount += $this->calculateLineItemTaxAmount($invoice, $line_item, $line_item->tax_3, 2);
        }

        return round($tax_amount, $precision);
    }

    protected function getNetTotal($invoice, object $line_item, int $precision)
    {
        return $this->calculateLineItemTotal($invoice, $line_item) - $this->getLineItemTaxTotal($invoice, $line_item, $precision);
    }

    protected function hasTaxes(object $line_item)
    {
        return !empty($line_item->unit_tax) || !empty($line_item->tax_2) || !empty($line_item->tax_2);
    }

    protected function getTaxRates($line_item)
    {
        $tax_rates = [];

        if (!empty($line_item->tax_rate_name)) {
            $tax_rates[] = $line_item->tax_rate_name;
        }

        if (!empty($line_item->tax_rate_name_2)) {
            $tax_rates[] = $line_item->tax_rate_name_2;
        }

        if (!empty($line_item->tax_rate_name_3)) {
            $tax_rates[] = $line_item->tax_rate_name_3;
        }

        return !empty($tax_rates) ? implode(',', $tax_rates) : '';
    }
}
