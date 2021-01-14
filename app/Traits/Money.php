<?php

namespace App\Traits;

use NumberFormatter;

/**
 * Class Number.
 */
trait Money
{
    public function getFormattedTotal()
    {
        return $this->formatCurrency($this->total, $this->customer);
    }

    public static function formatCurrency($value, $customer): string
    {
        $currency = $customer->currency;

        if (empty($currency)) {
            return true;
        }

        $locale = $customer->locale();
        $decimal_separator = isset($customer->country->decimal_mark) ? $customer->country->decimal_separator : $currency->decimal_mark;
        $thousand_separator = isset($customer->country->thousand_separator) ? $customer->country->thousand_separator : $currency->thousands_separator;

        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        //$fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, 'USD');
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $currency->precision); // decimals

        if (!empty($thousand_separator)) {
            $fmt->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $thousand_separator);
        }

        if (!empty($decimal_separator)) {
            $fmt->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, $decimal_separator);
        }

        $no_symbol = $customer->account->settings->show_currency_code === true;

        if ($no_symbol) {
            $fmt->setPattern(str_replace('¤#', '', $fmt->getPattern()));
            return $fmt->formatCurrency($value, $currency->iso_code) . ' ' . $currency->iso_code;
        }

        return $fmt->formatCurrency($value, $currency->iso_code);
    }

    public function getFormattedSubtotal()
    {
        return $this->formatCurrency($this->sub_total, $this->customer);
    }

    public function getFormattedBalance()
    {
        return $this->partial > 0
            ? $this->formatCurrency($this->partial, $this->customer)
            : $this->formatCurrency(
                $this->balance,
                $this->customer
            );
    }
}
