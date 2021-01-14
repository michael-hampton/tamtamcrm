<?php

namespace App\Traits;

use NumberFormatter;

/**
 * Class Number.
 */
trait MoneyVendor
{
    public function getFormattedTotal()
    {
        return $this->formatCurrency($this->total, $this->company);
    }

    public static function formatCurrency($value, $company): string
    {
        $currency = $company->currency;

        if (empty($currency)) {
            return true;
        }

        $locale = $company->locale();
        $decimal_separator = isset($company->country->decimal_mark) ? $company->country->decimal_separator : $currency->decimal_mark;
        $thousand_separator = isset($company->country->thousand_separator) ? $company->country->thousand_separator : $currency->thousands_separator;

        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        //$fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, 'USD');
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $currency->precision); // decimals

        if (!empty($thousand_separator)) {
            $fmt->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $thousand_separator);
        }

        if (!empty($decimal_separator)) {
            $fmt->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, $decimal_separator);
        }

        $no_symbol = $company->account->settings->show_currency_code === true;

        if ($no_symbol) {
            $fmt->setPattern(str_replace('¤#', '', $fmt->getPattern()));
            return $fmt->formatCurrency($value, $currency->iso_code) . ' ' . $currency->iso_code;
        }

        return $fmt->formatCurrency($value, $currency->iso_code);
    }

    public function getFormattedSubtotal()
    {
        return $this->formatCurrency($this->sub_total, $this->company);
    }

    public function getFormattedBalance()
    {
        return $this->formatCurrency($this->balance, $this->company);
    }
}
