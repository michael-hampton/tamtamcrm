import { ListGroup, ListGroupItem } from 'reactstrap'
import { translations } from '../../utils/_translations'
import React from 'react'
import FormatMoney from '../FormatMoney'
import InvoiceModel from '../../models/InvoiceModel'

export default function TotalsBox (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''
    const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
    const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
    const settings = user_account[0].account.settings
    const paid_to_date = props.entity.total - props.entity.balance

    const invoiceModel = new InvoiceModel(props.entity)
    const tax_total = invoiceModel.calculateTaxes(false)

    return <ListGroup className="col-6 mt-4">
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.paid_to_date}
            <span><FormatMoney amount={paid_to_date} customers={props.customers}/></span>
        </ListGroupItem>

        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.tax}
            <span><FormatMoney amount={tax_total} customers={props.customers}/></span>
        </ListGroupItem>

        {settings.show_tax_rate1 && props.entity.tax_rate > 0 &&
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {props.entity.tax_rate_name}
            <span><FormatMoney amount={invoiceModel.calculateTax(props.entity.tax_rate)}
                customers={props.customers}/> ({props.entity.tax_3})</span>
        </ListGroupItem>
        }
        {settings.show_tax_rate2 && props.entity.tax_2 > 0 &&
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {props.entity.tax_rate_name_2}
            <span><FormatMoney amount={invoiceModel.calculateTax(props.entity.tax_2)}
                customers={props.customers}/> ({props.entity.tax_3})</span>
        </ListGroupItem>
        }
        {settings.show_tax_rate3 && props.entity.tax_3 > 0 &&
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {props.entity.tax_rate_name_3}
            <span><FormatMoney amount={invoiceModel.calculateTax(props.entity.tax_3)}
                customers={props.customers}/> ({props.entity.tax_3})</span>
        </ListGroupItem>
        }

        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.discount}
            <span><FormatMoney amount={props.entity.discount_total} customers={props.customers}/></span>
        </ListGroupItem>
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.subtotal}
            <span><FormatMoney amount={props.entity.sub_total} customers={props.customers}/></span>
        </ListGroupItem>
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.total}
            <span><FormatMoney amount={props.entity.partial > 0 ? props.entity.partial : props.entity.total}
                customers={props.customers}/></span>
        </ListGroupItem>
        <ListGroupItem
            className={`${listClass} d-flex justify-content-between align-items-center`}>
            {translations.balance_due}
            <span><FormatMoney amount={props.entity.balance} customers={props.customers}/></span>
        </ListGroupItem>
    </ListGroup>
}
