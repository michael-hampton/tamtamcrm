import React from 'react'
import { translations } from '../../utils/_translations'
import FormatMoney from '../../common/FormatMoney'

export default function TotalsBox (props) {
    let total = props.invoice.sub_total - props.invoice.discount_total

    total += props.invoice.tax_total

    let tax_total = props.invoice.tax_total

    if (props.invoice.total_custom_values && props.invoice.total_custom_values > 0) {
        total += props.invoice.total_custom_values
    }

    if (props.invoice.gateway_fee && props.invoice.gateway_fee > 0) {
        let gateway_amount = props.invoice.gateway_fee

        if (props.invoice.gateway_percentage === true) {
            gateway_amount = total * props.invoice.gateway_fee / 100
        }

        total += gateway_amount
    }

    if (props.invoice.total_custom_tax && props.invoice.total_custom_tax > 0) {
        total += props.invoice.total_custom_tax
        tax_total += props.invoice.total_custom_tax
    }

    const paid_to_date = props.invoice.total - props.invoice.balance

    return (
        <div>
            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.tax}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={tax_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.discount}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={props.invoice.discount_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.subtotal}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={props.invoice.sub_total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.total}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={total}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.balance_due}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={props.invoice.balance}/>}</dd>
            </dl>

            <dl className="row d-flex mb-1">
                <dt className="flex-fill">{translations.paid_to_date}:</dt>
                <dd className="flex-fill text-right">{<FormatMoney amount={paid_to_date}/>}</dd>
            </dl>

        </div>
    )
}
