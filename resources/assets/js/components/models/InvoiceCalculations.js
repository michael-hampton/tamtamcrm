import axios from 'axios'
import moment from 'moment'
import BaseModel, { LineItem } from './BaseModel'
import { consts } from '../utils/_consts'
import { roundNumber } from '../utils/_formatting'

export const invoice_pdf_fields = ['$invoice.number', '$invoice.po_number', '$invoice.invoice_date', '$invoice.invoice_datetime', '$invoice.invoice_status', '$invoice.invoice_agent', '$invoice.due_date',
    '$invoice.balance', '$invoice.invoice_total', '$invoice.partial_due', '$invoice.custom1', '$invoice.custom2', '$invoice.custom3',
    '$invoice.custom4', '$invoice.surcharge1', '$invoice.surcharge2', '$invoice.surcharge3', '$invoice.surcharge4'
]

export default {

    _calculateTaxAmount (amount, rate, useInclusiveTaxes, precision = 2) {
        let taxAmount

        if (useInclusiveTaxes) {
            taxAmount = amount - (amount / (1 + (rate / 100)))
        } else {
            taxAmount = amount * rate / 100
        }

        return roundNumber(taxAmount, precision)
    },
    calculateSubtotal (precision = 2) {
        let total = 0.0

        this.fields.line_items.map((item) => {
            const qty = roundNumber(item.quantity, 4)
            const cost = roundNumber(item.unit_price, 4)

            if (!qty || !cost) {
                return 0
            }

            const discount = roundNumber(item.unit_discount, precision)
            let lineTotal = qty * cost

            if (discount !== 0) {
                if (this.fields.is_amount_discount) {
                    lineTotal -= discount
                } else {
                    lineTotal -= roundNumber(lineTotal * discount / 100, 4)
                }
            }

            total += roundNumber(lineTotal, precision)
        })

        return total
    },
    getItemTaxable (item, invoiceTotal, precision = 2) {
        const qty = roundNumber(item.quantity, 4)
        const cost = roundNumber(item.unit_price, 4)
        const itemDiscount = roundNumber(item.unit_discount, precision)
        let lineTotal = qty * cost

        if (this.fields.discount > 0) {
            if (this.fields.is_amount_discount) {
                if (invoiceTotal > 0) {
                    lineTotal -= roundNumber(lineTotal / invoiceTotal * this.fields.discount, 4)
                }
            }
        }

        if (itemDiscount > 0) {
            if (this.fields.is_amount_discount) {
                lineTotal -= itemDiscount
            } else {
                lineTotal -= roundNumber(lineTotal * itemDiscount / 100, 4)
            }
        }

        return lineTotal
    },
    calculateTaxes (usesInclusiveTaxes, precision = 2) {
        let total = this.calculateSubtotal(precision)
        let taxAmount = 0

        this.fields.line_items.map((item) => {
            const taxRate1 = roundNumber(item.unit_tax, 3)
            const taxRate2 = roundNumber(item.tax_2, 3)
            const taxRate3 = roundNumber(item.tax_3, 3)

            const lineTotal = this.getItemTaxable(item, total, precision)

            if (taxRate1 > 0) {
                taxAmount += this._calculateTaxAmount(
                    lineTotal, taxRate1, this.settings.inclusive_taxes, precision)
            }

            if (taxRate2 > 0) {
                taxAmount += this._calculateTaxAmount(
                    lineTotal, taxRate2, this.settings.inclusive_taxes, precision)
            }

            if (taxRate3 > 0) {
                taxAmount += this._calculateTaxAmount(
                    lineTotal, taxRate3, this.settings.inclusive_taxes, precision)
            }
        })

        if (this.fields.discount > 0) {
            if (this.fields.is_amount_discount) {
                total -= roundNumber(this.fields.discount, precision)
            } else {
                total -= roundNumber(total * this.fields.discount / 100, precision)
            }
        }

        if (this.fields.shipping_cost > 0 && this.fields.shipping_cost_tax > 0) {
            total += roundNumber(this.fields.shipping_cost, precision)
        }

        if (this.fields.transaction_fee > 0 && this.fields.transaction_fee_tax > 0) {
            total += roundNumber(this.fields.transaction_fee, precision)
        }

        if (this.fields.tax_rate > 0) {
            taxAmount +=
                this._calculateTaxAmount(total, this.fields.tax_rate, this.settings.inclusive_taxes, precision)
        }

        if (this.fields.tax_2 > 0) {
            taxAmount +=
                this._calculateTaxAmount(total, this.fields.tax_2, this.settings.inclusive_taxes, precision)
        }

        if (this.fields.tax_3 > 0) {
            taxAmount +=
                this._calculateTaxAmount(total, this.fields.tax_3, this.settings.inclusive_taxes, precision)
        }

        return roundNumber(taxAmount, precision)
    },
    calculateTax (tax_amount) {
        const a_total = parseFloat(this.fields.total)
        const tax_percentage = parseFloat(a_total) * parseFloat(tax_amount) / 100

        const precision = this.currency.precision || 2

        return Math.round(tax_percentage, precision)
    },

    calculateTotal (precision = 2) {
        let total = this.calculateSubtotal(precision)
        const sub_total = total
        let itemTax = 0.0
        let discount_total = 0

        this.fields.line_items.map((item) => {
            const qty = roundNumber(item.quantity, 4)
            const cost = roundNumber(item.unit_price, 4)
            const itemDiscount = roundNumber(item.unit_discount, precision)
            const taxRate1 = roundNumber(item.unit_tax, 3)
            const taxRate2 = roundNumber(item.tax_2, 3)
            const taxRate3 = roundNumber(item.tax_3, 3)
            let lineTotal = qty * cost

            if (itemDiscount > 0) {
                if (this.fields.is_amount_discount) {
                    discount_total += itemDiscount
                    lineTotal -= itemDiscount
                } else {
                    const discount = roundNumber(lineTotal * itemDiscount / 100, 4)
                    discount_total += discount
                    lineTotal -= discount
                }
            }

            if (this.fields.discount > 0) {
                if (this.fields.is_amount_discount) {
                    if (total > 0) {
                        const discount = roundNumber(lineTotal / total * this.fields.discount, 4)
                        discount_total += discount
                        lineTotal -= discount
                    }
                }
            }
            if (taxRate1 > 0) {
                itemTax += roundNumber(lineTotal * taxRate1 / 100, precision)
            }
            if (taxRate2 > 0) {
                itemTax += roundNumber(lineTotal * taxRate2 / 100, precision)
            }
            if (taxRate3 > 0) {
                itemTax += roundNumber(lineTotal * taxRate2 / 100, precision)
            }
        })

        if (this.fields.discount > 0) {
            if (this.fields.is_amount_discount) {
                const discount = roundNumber(this.fields.discount, precision)
                discount_total += discount
                total -= discount
            } else {
                const discount = roundNumber(total * this.fields.discount / 100, precision)
                discount_total += discount
                total -= discount
            }
        }

        if (this.fields.shipping_cost > 0 && this.fields.shipping_cost_tax) {
            total += roundNumber(this.fields.shipping_cost, precision)
        }

        if (this.fields.transaction_fee > 0 && this.fields.transaction_fee_tax) {
            total += roundNumber(this.fields.transaction_fee, precision)
        }

        if (!this.settings.inclusive_taxes) {
            const taxAmount1 = roundNumber(total * this.fields.tax_rate / 100, precision)
            const taxAmount2 = roundNumber(total * this.fields.tax_2 / 100, precision)
            const taxAmount3 = roundNumber(total * this.fields.tax_3 / 100, precision)

            total += itemTax + taxAmount1 + taxAmount2 + taxAmount3
        }

        if (this.fields.shipping_cost > 0 && !this.fields.shipping_cost_tax) {
            total += roundNumber(this.fields.shipping_cost, precision)
        }

        if (this.fields.transaction_fee > 0 && !this.fields.transaction_fee_tax) {
            total += roundNumber(this.fields.transaction_fee, precision)
        }

        return {
            total: total,
            sub_total: sub_total,
            discount_total: discount_total
        }
    }
}
