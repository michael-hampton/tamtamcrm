import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import { consts, invoiceStatusColors, invoiceStatuses } from '../utils/_consts'
import InvoiceModel from '../models/InvoiceModel'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'number',
        'customer_id',
        'date',
        'due_date',
        'total',
        'balance',
        'status_id'
    ]
}

export default function InvoicePresenter (props) {
    const { field, entity } = props

    console.log('entity', entity)

    const objInvoiceModel = new InvoiceModel(entity, props.customers)
    const is_late = objInvoiceModel.isLate()
    const is_viewed = objInvoiceModel.isViewed

    const entity_status = (is_viewed === true) ? (consts.invoice_status_viewed) : ((is_late === true) ? (100) : (entity.status_id))

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        <Badge color={invoiceStatusColors[entity_status]}>{invoiceStatuses[entity_status]}</Badge>))

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                data-label={field}>{assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''}</td>
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                data-label={field}>{`${user[0].first_name} ${user[0].last_name}`}</td>
        }
        case 'exchange_rate':
        case 'balance':
        case 'total':
        case 'discount_total':
        case 'tax_total':
        case 'sub_total':
            return <FormatMoney customer_id={entity.customer_id} customers={props.customers} amount={entity[field]}/>
        case 'status_field':
            return status
        case 'date':
        case 'due_date':
        case 'date_to_send':
        case 'created_at':
            return <FormatDate field={field} date={entity[field]}/>

        case 'status_id':
            return status

        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return customer.name
        }

        case 'currency_id': {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(props.entity.currency_id))
            return currency.length ? currency[0].iso_code : ''
        }

        default:
            return entity[field]
    }
}
