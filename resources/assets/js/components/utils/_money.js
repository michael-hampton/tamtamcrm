import { consts } from './_consts'

export function getExchangeRate (from_currency, to_currency) {
    const currencyMap = JSON.parse(localStorage.getItem('currency'))
    return getExchangeRateWithMap(currencyMap, from_currency, to_currency)
}

export function getExchangeRateWithMap (currencies, from_currency_id, to_currency_id) {
    const fromCurrency = currencies.filter(currency => currency.id === parseInt(from_currency_id))
    const toCurrency = currencies.filter(currency => currency.id === parseInt(to_currency_id))

    console.log('from', fromCurrency)
    console.log('to', toCurrency)

    if (parseInt(from_currency_id) === consts.currency_pound) {
        return toCurrency[0].exchange_rate
    }

    if (parseInt(to_currency_id) === consts.currency_pound) {
        return 1 / (fromCurrency[0].exchange_rate || 1)
    }

    return toCurrency[0].exchange_rate * (1 / fromCurrency[0].exchange_rate)
}
