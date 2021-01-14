<?php

namespace App\Components\Currency;

use App\Models\Currency;
use App\Repositories\CurrencyRepository;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

class CurrencyConverter
{
    private CurrencyRepository $currency_repository;

    private $base_currency = null;

    private $exchange_currency = null;

    private float $amount = 0.00;

    private $date;

    /**
     * @var array
     */
    private array $exchange_rates = [];

    public function __constructor(CurrencyRepository $currency_repository = null)
    {
        $this->currency_repository = $currency_repository;
    }

    public function setBaseCurrency(Currency $currency): self
    {
        $this->base_currency = $currency;
        return $this;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setExchangeCurrency(Currency $currency): self
    {
        $this->exchange_currency = $currency;
        return $this;
    }

    public function setDate($date): self
    {
        $this->date = Carbon::parse($date);
        return $this;
    }

    public function calculate()
    {
        if (empty($this->amount) || empty($this->base_currency)) {
            return false;
        }

        $exchangeRates = new ExchangeRate();

        $converted_amount = $exchangeRates->convert(
            $this->amount,
            $this->base_currency->iso_code,
            $this->exchange_currency->iso_code,
            Carbon::now()
        );

        return $converted_amount;
    }

    /**
     * @param string $code
     * @return float
     */
    public function getExchangeRate(string $code): float
    {
        if (empty($this->exchange_rates)) {
            $this->download();
        }

        if (!empty($this->exchange_rates[$code])) {
            return $this->exchange_rates[$code];
        }

        return false;
    }

    public function download()
    {
        $client = new Client();
        $response = $client->get(
            'https://openexchangerates.org/api/latest.json?app_id=' . config('taskmanager.currency_converter_key')
        );
        $list = json_decode($response->getBody(), true);
        $this->exchange_rates = $list['rates'];
    }

    private function getCurrency($currency)
    {
        return $this->currency_repository->findCurrencyById($currency);
    }
}
