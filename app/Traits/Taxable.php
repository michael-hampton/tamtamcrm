<?php
trait Taxable {

    private array $map = [];

    public function getTaxes (int $precision)
    {
       $taxes = [];
       $taxable = $this->calculateTaxes($usesInclusiveTaxes, $precision);
       $paidAmount = $this->total - $this->balance;

    if ($this->tax_rate > 0) {
      $invoiceTaxAmount = $taxable[$this->tax_rate_name];
      $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
          ? ($this->getAmountPaid() / $this->total * $invoiceTaxAmount)
          : 0.0;
      $this->calculateTax(
          $taxes, $this->tax_rate_name, $this->tax_rate, $invoiceTaxAmount, $invoicePaidAmount);
    }

    if ($this->tax_2 > 0) {
      $invoiceTaxAmount = $taxable[$this->tax_rate_name_2];
      $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
          ? ($this->getAmountPaid() / $this->total * $invoiceTaxAmount)
          : 0.0;
      $this->calculateTax(
          $taxes, $this->tax_rate_name_2, $this->tax_rate, $invoiceTaxAmount, $invoicePaidAmount);
    }

    if ($this->tax_3 > 0) {
      $invoiceTaxAmount = $taxable[$this->tax_rate_name_3];
      $invoicePaidAmount = ($this->total * $invoiceTaxAmount != 0)
          ? ($this->getAmountPaid() / $this->total * $invoiceTaxAmount)
          : 0.0;
      $this->calculateTax(
          $taxes, $this->tax_rate_name_3, $this->tax_rate, $invoiceTaxAmount, $invoicePaidAmount);
    }

    foreach ($this->line_items as $item) {
      if ($item->tax_rate > 0) {
        $itemTaxAmount = $taxable[item->tax_rate_name];
        $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
            ? ($paidAmount / $this->total * $itemTaxAmount)
            : 0.0;
        $this->calculateTax(
            $taxes, $item->tax_rate_name, $item->tax_rate, $itemTaxAmount, $itemPaidAmount);
      }
  
      if ($item->tax_2 > 0) {
        $itemTaxAmount = $taxable[item->tax_rate_name_2];
        $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
            ? ($paidAmount / $this->total * $itemTaxAmount)
            : 0.0;
        $this->calculateTax(
            $taxes, $item->tax_rate_name_2, $item->tax_2, $itemTaxAmount, $itemPaidAmount);
      }

      if ($item->tax_3 > 0) {
        $itemTaxAmount = $taxable[item->tax_rate_name_3];
        $itemPaidAmount = !empty($this->total) &&
                !empty($itemTaxAmount) &&
                $this->total * $itemTaxAmount > 0
            ? ($paidAmount / $this->total * $itemTaxAmount)
            : 0.0;
        $this->calculateTax(
            $taxes, $item->tax_rate_name_3, $item->tax_3, $itemTaxAmount, $itemPaidAmount);
      }
     
    }

    return taxes;
    }

    private function calculateTax(array $map, string $name, float $rate, float $amount, float $paid) {
    if (empty($amount)) {
      return false;
    }

    $key = $rate . ' ' . $name;

    if(!isset($this->map[$key])) {
        $this->map[$key] = [
              'name' => $name,
              'rate' => $rate,
              'amount' => 0,
              'paid' => 0
            ];
    }

    $this->map[$key]['amount'] += $amount;
    $this->map[$key]['paid'] += $paid;

  }

 private function calculateTaxAmount(float $amount, float $rate, bool $useInclusiveTaxes) {
    $taxAmount = 0;

    if ($this->uses_inclusive_taxes) {
      return $amount - ($amount / (1 + ($rate / 100)));
    }
      
    return $amount * $rate / 100;
  }

  private function calculateTaxes(bool $useInclusiveTaxes) {
    $total = $this->calculateSubtotal();
    $taxAmount = 0;
    $map = [];

    foreach($this->line_items as $item) {

      $lineTotal = $this->getItemTaxable($item, $total);

      if ($item->tax_rate > 0) {
        $taxAmount = $this->calculateTaxAmount(
            $lineTotal, $item->tax_rate, $this->uses_inclusive_taxes);

            $map[$item->tax_rate_name] = !isset($map[$item->tax_rate_name]) ? $taxAmount : $map[$item->tax_rate_name] += $taxAmount;
      }

      if ($item->tax_2 > 0) {
        $taxAmount = $this->calculateTaxAmount(
            $lineTotal, $item->tax_2, $this->uses_inclusive_taxes);
        $map[$item->tax_rate_name_2] = !isset($map[$item->tax_rate_name_2]) ? $taxAmount : $map[$item->tax_rate_name_2] += $taxAmount;
      }

      if ($item->tax_3 != 0) {
        $taxAmount = $this->calculateTaxAmount(
            $lineTotal, $item->tax_3, $this->uses_inclusive_taxes);
        $map[$item->tax_rate_name_3] = !isset($map[$item->tax_rate_name_3]) ? $taxAmount : $map[$item->tax_rate_name_3] += $taxAmount;
      }
    }

    if ($this->discount_total > 0) {
      if ($this->is_amount_discount) {
        $total -= $this->discount_total;
      } else {
        $total -= $total * $discount_total / 100;
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
      $map[$this->tax_rate_name_3] = !isset($map[$this->tax_rate_name_3]) ? $taxAmount : $map[$this>tax_rate_name_3] += $taxAmount;
    }

    return $map;
  }

  private function getItemTaxable($item, float $invoiceTotal) {
    $lineTotal = $item->quantity * $item->unit_cost;

    if ($this->discount_total > 0) {
      if ($this->is_amount_discount) {
        if ($invoiceTotal > 0) {
          $lineTotal -= $lineTotal / $invoiceTotal * $this->discount_total;
        }
      }
    }

    if ($item->unit_discount > 0) {
      if ($this->is_amount_discount) {
        $lineTotal -= $item->unit_discount;
      } else {
        $lineTotal -= $lineTotal * $itemDiscount / 100;
      }
    }

    return $lineTotal;
  }

private function calculateTotal() {
    $total = $this->calculateSubtotal();
    $itemTax = 0.0;

    foreach($this->line_items as $item) {
      $taxRate1 = round($item->tax_rate, 3);
      $taxRate2 = round($item->tax_2, 3);
      $lineTotal = $item->quantity * $item->unit_cost;

      if ($item->unit_discount > 0) {
        if ($this->is_amount_discount) {
          $lineTotal -= $item->unit_discount;
        } else {
          $lineTotal -= round($lineTotal * $item->unit_discount / 100, 4);
        }
      }

      if ($this->discount_total > 0) {
        if ($this->is_amount_discount) {
          if ($this->total != 0) {
            $lineTotal -= $lineTotal / $this->total * $this->discount_total;
          }
        }
      }
      if ($tax_rate > 0) {
        $itemTax += $lineTotal * $tax_rate / 100;
      }
      if ($tax_2 > 0) {
        $itemTax += $lineTotal * $tax_2 / 100;
      }
    }

    if ($this->discount_total > 0) {
      if ($this->is_amount_discount) {
        $total -= $this->discount_total;
      } else {
        $total -= $this->total * $this->discount_total / 100;
      }
    }

    if ($this->shipping_cost > 0 && $this->shipping_cost_tax > 0) {
      $total += $this->shipping_cost;
    }

    if ($this->transaction_fee > 0 && $this->transaction_fee_tax > 0) {
      $total += $this->transaction_fee;
    }

    if (!$this->uses_inclusive_taxes) {
      $taxAmount1 = $total * $this->tax_rate / 100;
      $taxAmount2 = $total * $tax_2 / 100;

      $total += $itemTax + $taxAmount1 + $taxAmount2;
    }

   if ($this->shipping_cost > 0 && empty($this->shipping_cost_tax)) {
      $total += $this->shipping_cost;
    }

   if ($this->transaction_fee > 0 && empty($this->transaction_fee_tax)) {
      $total += $this->transaction_fee;
    }

    return $total;
  }

  private function calculateSubtotal() {
   $total = 0.0;

    foreach($this->line_items as item) {
     

      $lineTotal = $item->quantity * $item->unit_cost;

      if ($item->unit_discount > 0) {
        if ($this->is_amount_discount) {
          $lineTotal -= $item->unit_discount;
        } else {
          $lineTotal -= $lineTotal * $discount / 100;
        }
      }

      $total += $lineTotal;
    }

    return $total;
  }

}
