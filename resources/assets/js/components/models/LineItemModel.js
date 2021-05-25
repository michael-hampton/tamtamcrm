import BaseModel from "./BaseModel";
import {roundNumber} from "../utils/_formatting";

export default class LineItemModel extends BaseModel{

    constructor (line_item, invoice) {
        super();
        this.line_item = line_item
        this.invoice = invoice
    }

    netTotal(invoice, precision) {
        return this.total() - this.taxAmount(invoice, precision);
    }

    taxAmount() {
        let tax_total = 0

        if (this.line_item.unit_tax > 0) {
            tax_total += this.calculateTaxAmount(this.line_item.unit_tax, 2)
        }

        if (this.line_item.tax_2 && this.line_item.tax_2 > 0) {
            tax_total += this.calculateTaxAmount(this.line_item.tax_2, 2)
        }

        if (this.line_item.tax_3 && this.line_item.tax_3 > 0) {
            tax_total += this.calculateTaxAmount(this.line_item.tax_3, 2)
        }

        return tax_total
    }

    calculateTaxAmount(rate, precision) {
        let taxAmount;

        if (rate === 0) {
            return 0;
        }

        const lineTotal = this.total();

        if (this.settings.inclusive_taxes) {
            taxAmount = lineTotal - (lineTotal / (1 + (rate / 100)));
        } else {
            taxAmount = lineTotal * rate / 100;
        }

        return roundNumber(taxAmount, precision);
    }

    discountAmount() {
        if (!this.line_item.unit_discount || this.line_item.unit_discount < 0) {
            return 0;
        }

        let total = this.line_item.quantity * this.line_item.unit_price

        if (this.line_item.unit_discount > 0) {
            if (this.invoice.is_amount_discount === true) {
                return this.line_item.unit_discount;
            } else {
                return this.line_item.unit_discount / 100 * total;
            }
        }
    }

    total() {
        let total = this.line_item.quantity * this.line_item.unit_price

        total = total - this.discountAmount()

        return roundNumber(total, 2)
    }
}