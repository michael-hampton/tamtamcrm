<?php
trait Taxable {

private function calculateTotal(int $precision) {
    $total = $this->calculateSubtotal($precision);
    $itemTax = 0.0;

    foreach($this->line_items as $item) {
      $qty = round($item->quantity, 4);
      $cost = round($item->cost, 4);
      $itemDiscount = round($item->discount, $precision);
      $taxRate1 = round($item->tax_rate, 3);
      $taxRate2 = round($item->tax_2, 3);
      $lineTotal = $qty * $cost;

      if ($itemDiscount != 0) {
        if ($this->is_amount_discount) {
          $lineTotal -= $itemDiscount;
        } else {
          $lineTotal -= round($lineTotal * $itemDiscount / 100, 4);
        }
      }

      if ($this->discount_total > 0) {
        if ($this->is_amount_discount) {
          if ($this->total != 0) {
            $lineTotal -= round($lineTotal / $this->total * $this->discount_total, 4);
          }
        }
      }
      if ($tax_rate > 0) {
        $itemTax += round($lineTotal * $tax_rate / 100, $precision);
      }
      if ($tax_2 > 0) {
        $itemTax += round($lineTotal * $tax_2 / 100, $precision);
      }
    }

    if ($this->discount_total > 0) {
      if ($this->is_amount_discount) {
        $total -= round($this->discount_total, $precision);
      } else {
        $total -= round($this->total * $this->discount_total / 100, $precision);
      }
    }

    if ($this->shipping_cost > 0 && $this->shipping_cost_tax > 0) {
      $total += round($this->shipping_cost, $precision);
    }

    if ($this->transaction_fee > 0 && $this->transaction_fee_tax > 0) {
      $total += round($this->transaction_fee, $precision);
    }

    if (!$this->uses_inclusive_taxes) {
      $taxAmount1 = round($total * $this->tax_rate / 100, $precision);
      $taxAmount2 = round($total * $tax_2 / 100, $precision);

      $total += $itemTax + $taxAmount1 + $taxAmount2;
    }

   if ($this->shipping_cost > 0 && empty($this->shipping_cost_tax)) {
      $total += round($this->shipping_cost, precision);
    }

   if ($this->transaction_fee > 0 && empty($this->transaction_fee_tax)) {
      $total += round($this->transaction_fee, $precision);
    }

    return $total;
  }

  private function calculateSubtotal(int $precision) {
   $total = 0.0;

    foreach($this->line_items as item) {
      $qty = round(item->quantity, 4);
      $cost = round(item->cost, 4);
      $discount = round(item->discount, $precision);

      $lineTotal = $qty * $cost;

      if ($discount > 0) {
        if ($this->is_amount_discount) {
          $lineTotal -= $discount;
        } else {
          $lineTotal -= round($lineTotal * $discount / 100, 4);
        }
      }

      $total += round($lineTotal, $precision);
    }

    return $total;
  }

}
