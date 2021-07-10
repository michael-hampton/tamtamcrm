<?php

namespace App\Traits;

use App\Components\Currency\CurrencyConverter;
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

    /**
     * @param $entity
     * @param float|null $amount
     * @param bool $use_live_currencies
     * @return mixed
     */
    public function convertCurrencies($entity, ?float $amount = null, bool $use_live_currencies = false)
    {
        if (!$use_live_currencies) {


            if(empty($entity->exchange_rate)) {
                $exchange_rate = $entity->customer->getExchangeRate();
                $entity->exchange_rate = !empty($exchange_rate) ? $exchange_rate : 1;

            }

            if (empty($entity->currency_id)) {
                $entity->currency = !empty($entity->customer->currency_id) ? (int)$entity->customer->currency_id : (int)$entity->account->settings->currency_id;
            }
            
            return $entity;
        }

        if (empty($amount) || (int)$entity->account->getCurrency()->id === (int)$entity->customer->currency->id) {
            return $entity;
        }

        $converted_amount = $objCurrencyConverter = (new CurrencyConverter())
            ->setAmount($amount)
            ->setBaseCurrency($entity->account->getCurrency())
            ->setExchangeCurrency($entity->customer->currency)
            ->setDate(now())
            ->calculate();

        if ($converted_amount) {
            $entity->exchange_rate = $converted_amount;
            $entity->currency_id = $entity->account->getCurrency()->id;
            $entity->exchange_currency_id = $entity->customer->currency->id;
        }

        return $entity;
    }

    public function getAmountInWords(float $amount, ?string $locale = null)
    {
        $amount    = number_format($amount, $this->currency_decimals, '.', '');
        $formatter = new NumberFormatter($locale ?? App::getLocale(), NumberFormatter::SPELLOUT);

        $value = explode('.', $amount);

        $integer_value  = (int) $value[0] !== 0 ? $formatter->format($value[0]) : 0;
        $fraction_value = isset($value[1]) ? $formatter->format($value[1]) : 0;

        if ($this->currency_decimals <= 0) {
            return sprintf('%s %s', ucfirst($integer_value), strtoupper($this->currency_code));
        }

        return sprintf(
            '%s %s and %s %s',
            ucfirst($integer_value),
            strtoupper($this->currency_code),
            $fraction_value,
            $this->currency_fraction
        );
    }
}
