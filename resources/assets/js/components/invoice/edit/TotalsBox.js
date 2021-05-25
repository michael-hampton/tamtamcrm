import React from 'react'
import { translations } from '../../utils/_translations'
import FormatMoney from '../../common/FormatMoney'
import InvoiceModel from "../../models/InvoiceModel";

export default function TotalsBox (props) {
    const invoiceModel = new InvoiceModel(props.invoice)
    const tax_total = invoiceModel.calculateTaxes(props.settings.inclusive_taxes)
    const totals = invoiceModel.calculateTotal()

    return (
        <div>
            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.tax}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={tax_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.discount}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={totals.discount_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.subtotal}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={totals.sub_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.total}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={totals.total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.balance_due}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={props.invoice.balance}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.amount_paid}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={props.invoice.amount_paid}/>}</dd>
            </dl>

        </div>
    )
}
