import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import { creditStatusColors, creditStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'status_id',
        'number',
        'customer_id',
        'amount',
        'date',
        'balance'
    ]
}

export default function CreditPresenter (props) {
    const { field, entity } = props

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        <Badge color={creditStatusColors[entity.status_id]}>{creditStatuses[entity.status_id]}</Badge>))

    switch (field) {
        case 'status_field':
            return status
        case 'status_id':
            return status
        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return customer.name
        }
        case 'date':
        case 'due_date':
        case 'created_at':
            return <FormatDate field={field} date={entity[field]}/>
        case 'balance':
        case 'total':
        case 'discount_total':
        case 'tax_total':
        case 'sub_total':
        case 'exchange_rate':
            return <FormatMoney customer_id={entity.customer_id} customers={props.customers} amount={entity[field]}/>
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }

        case 'currency_id': {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(props.entity.currency_id))
            return currency.length ? currency[0].iso_code : ''
        }
        default:
            return entity[field]
    }
}
