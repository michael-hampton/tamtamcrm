<?php

namespace App\Rules\Order;

use App\Models\Promocode;
use Illuminate\Contracts\Validation\Rule;

class OrderTotals implements Rule
{
    private $request;

    /**
     * @var array
     */
    private $arrErrors = [];

    private $sub_total = 0;

    /**
     * Create a new rule instance.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->validate()) {
            return false;
        }

        return true;
    }

    private function validate(): bool
    {
        $this->calculateSubTotal();
        $this->calculateTax();
        $this->calculateDiscount();
        $this->calculateShipping();
        $this->checkTotals();

        return count($this->arrErrors) === 0;
    }

    private function calculateSubTotal()
    {
        $this->sub_total = 0;

        foreach ($this->request['line_items'] as $product) {
            if (is_array($product)) {
                $product = (object)$product;
            }

            $this->sub_total += ($product->unit_price * $product->quantity);
        }

        return true;
    }

    private function calculateTax()
    {
        $tax_rate = (float)str_replace('%', '', $this->request['tax_rate']);

        $tax = (($tax_rate / 100) * $this->sub_total);

        $this->tax = round($tax, 2);
        $this->sub_total += $this->tax;

        return true;
    }

    private function calculateDiscount()
    {
        if (!isset($this->request['voucher_code'])) {
            return true;
        }

        $voucher = Promocode::whereCode($this->request['voucher_code'])->first();
        $voucher_amount = $voucher->reward;
        $voucher_type = $voucher->amount_type;

        $discount_amt = $voucher_type === 'pct' ? $this->sub_total * ($voucher_amount / 100) : $voucher_amount;

        if ($discount_amt <= 0) {
            return true;
        }

        if ($discount_amt > $this->sub_total) {
            $this->arrErrors[] = 'The discount amount is more that the order total';
        }

        $this->sub_total -= $discount_amt;

        return true;
    }

    private function calculateShipping()
    {
        if (empty($this->request['shipping_cost'])) {
            return true;
        }

        $this->sub_total += (float)$this->request['shipping_cost'];

        return true;
    }

//    private function addTransactionFee()
//    {
//        $transaction_fee = 0;
//
//        foreach ($this->request['products'] as $request_product) {
//            $product = Product::whereId($request_product['product_id'])->first();
//
//            $account = $product->account;
//            $transaction_fee = $account->transaction_fee;
//        }
//    }

    private function checkTotals()
    {
        $total = round($this->sub_total, 2);

        $match = (float)$total === (float)$this->request['total'];

        if (!$match) {
            $this->arrErrors[] = "Totals do not match";
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message()
    {
        return $this->arrErrors;
    }
}
