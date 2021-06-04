import React from 'react'
import InvoiceModel from '../models/InvoiceModel'
import LineItemModel from '../models/LineItemModel'

export const CalculateTotal = (props) => {
    const { invoice } = props

    const invoiceModel = new InvoiceModel(invoice)
    const totals = invoiceModel.calculateTotal()

    return {
        total: totals.total,
        discount_total: totals.discount_total,
        tax_total: invoiceModel.calculateTaxes(),
        sub_total: totals.sub_total
    }
}

export const CalculateSurcharges = (props) => {
    let total = 0
    let tax_total = 0
    const { surcharges } = props

    const tax = parseFloat(surcharges.tax)

    if (surcharges.transaction_fee && surcharges.transaction_fee > 0) {
        total += surcharges.transaction_fee
    }

    if (surcharges.transaction_fee && surcharges.transaction_fee > 0 && surcharges.transaction_fee_tax === true && tax > 0) {
        tax_total += surcharges.transaction_fee * (tax / 100)
    }

    if (surcharges.shipping_cost && surcharges.shipping_cost > 0) {
        total += surcharges.shipping_cost
    }

    if (surcharges.shipping_cost && surcharges.shipping_cost > 0 && surcharges.shipping_cost === true && tax > 0) {
        tax_total += surcharges.custom_surcharge2 * (tax / 100)
    }

    return { total_custom_values: total, total_custom_tax: tax_total }
}

export const CalculateLineTotals = (props) => {
    const { currentRow, settings, invoice } = props

    const lineItemModel = new LineItemModel(currentRow, invoice)

    currentRow.sub_total = currentRow.unit_price
    currentRow.tax_total = lineItemModel.taxAmount()

    return currentRow
}
