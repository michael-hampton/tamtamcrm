import React from 'react'

export const CalculateTotal = (props) => {
    let total = 0
    let discount_total = 0
    let tax_total = 0
    let sub_total = 0
    let lexieTotal = 0
    const { invoice } = props

    invoice.line_items.map((product) => {
        const quantity = product.quantity === 0 ? 1 : product.quantity

        const line_total = product.unit_price * quantity
        total += line_total
        sub_total += line_total
        lexieTotal += line_total

        if (product.unit_discount > 0 && invoice.discount === 0) {
            const n = parseFloat(total)

            if (invoice.is_amount_discount === true) {
                discount_total += parseFloat(product.unit_discount)
            } else {
                const percentage = n * product.unit_discount / 100
                discount_total += percentage
                // lexieTotal -= discount_total
            }

            lexieTotal -= discount_total
        }

        if (product.unit_tax > 0 && invoice.tax === 0) {
            const tax_percentage = lexieTotal * product.unit_tax / 100
            tax_total += tax_percentage
        }
    })

    if (invoice.discount > 0) {
        const discount_percentage = parseFloat(invoice.total) * parseFloat(invoice.discount) / 100
        discount_total += discount_percentage
        // total -= discount_percentage
    }

    if (invoice.tax > 0) {
        const a_total = invoice.total_custom_values > 0 ? parseFloat(invoice.total_custom_values) + parseFloat(lexieTotal) : parseFloat(lexieTotal)
        const tax_percentage = parseFloat(a_total) * parseFloat(invoice.tax) / 100
        tax_total += tax_percentage
    }

    if (invoice.tax_2 && invoice.tax_2 > 0) {
        const a_total = invoice.total_custom_values > 0 ? parseFloat(invoice.total_custom_values) + parseFloat(lexieTotal) : parseFloat(lexieTotal)
        const tax_percentage = parseFloat(a_total) * parseFloat(invoice.tax_2) / 100
        tax_total += tax_percentage
    }

    if (invoice.tax_3 && invoice.tax_3 > 0) {
        const a_total = invoice.total_custom_values > 0 ? parseFloat(invoice.total_custom_values) + parseFloat(lexieTotal) : parseFloat(lexieTotal)
        const tax_percentage = parseFloat(a_total) * parseFloat(invoice.tax_3) / 100
        tax_total += tax_percentage
    }

    return {
        total: total,
        discount_total: discount_total,
        tax_total: tax_total,
        sub_total: sub_total
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

    const price = currentRow.unit_price
    let lexieTotal = 0

    if (price < 0) {
        return false
    }

    let total = price
    const unit_discount = currentRow.unit_discount
    const unit_tax = currentRow.unit_tax
    const uses_inclusive_taxes = settings.inclusive_taxes

    const quantity = currentRow.quantity

    if (quantity > 0) {
        total = price * quantity
        lexieTotal += price * quantity
    }
    if (unit_discount > 0 && invoice.discount === 0) {
        const n = parseFloat(total)

        if (invoice.is_amount_discount === true) {
            lexieTotal -= unit_discount
        } else {
            const percentage = n * unit_discount / 100
            lexieTotal -= percentage
        }
    }

    if (unit_tax > 0 && invoice.tax === 0) {
        const tax_percentage = lexieTotal * unit_tax / 100
        currentRow.tax_total = tax_percentage

        if (uses_inclusive_taxes === false) {
            total += tax_percentage
        }
    }

    currentRow.sub_total = total

    return currentRow
}
