<?php

namespace App\Transformations;

use App\Models\Category;
use App\Models\Product;
use App\Traits\MonthlyPayments;
use Illuminate\Http\Request;

trait LoanProductTransformable
{

    use MonthlyPayments;

    /**
     * Transform the product
     *
     * @param Product $product
     * @param Category $parentCategory
     * @param Request $request
     * @return Product
     */
    protected function transformLoanProduct(Product $product, Category $parentCategory, Request $request)
    {
        $prod = new Product;


        $attributes = $product->attributes->first();

        if (!$attributes || $attributes->count() === 0) {
            return false;
        }

        $prod->id = (int)$product->id;
        $prod->name = $product->name;
        $prod->sku = $product->sku;
        $prod->slug = $product->slug;
        $prod->description = $product->description;
        $prod->price = $product->price;
        $prod->status = $product->status;
        $prod->company_id = (int)$product->company_id;
        $prod->brand = $product->company->name;
        $prod->category_ids = $product->categories()->pluck('category_id')->all();

        $interest_rate = $attributes->interest_rate;
        $value = $request->valued_at;
        $downpayment = $attributes->minimum_downpayment;
        $years = $attributes->number_of_years;
        $months = $attributes->payable_months;

        $prod->interest_rate = $interest_rate;
        $prod->value = $value;
        $prod->downpayment_percentage = $downpayment;
        $prod->years = $years;
        $prod->months = $months;


        if ($parentCategory->name === 'Mortgages') {
            $months = 0;
            $prod->frequency = 'Monthly';

            $newprice = ($value * ((100 - $downpayment) / 100));
            $downpayment_cost = $this->calculateDownpayment($downpayment, $value);
            $prod->downpayment = $downpayment_cost;

            $new_total = $this->calculateTotal($newprice, $interest_rate);
            $number_of_months = $years * 12;

            $monthly_rate = $this->calculateMonthlyCost($number_of_months, $new_total);
            $prod->monthly_payment = $monthly_rate;
            $prod->total = $new_total;

            return $prod;
        }

        $new_total = $this->calculateTotal($value, $interest_rate);
        $prod->total = $new_total;

        $monthly_payment = $this->calculateMonthlyCost($months, $new_total);
        $prod->monthly_payment = $monthly_payment;

        return $prod;
    }

}
