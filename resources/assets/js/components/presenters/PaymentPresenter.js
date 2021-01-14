import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import PaymentModel from '../models/PaymentModel'
import { paymentStatusColors, paymentStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'number',
        'customer_id',
        'date',
        'amount',
        'reference_number',
        'invoices',
        'credits',
        'status_id'
    ]
}

export default function PaymentPresenter (props) {
    const { field, entity } = props

    const paymentModel = new PaymentModel(entity.invoices, entity, entity.credits)

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        <Badge color={paymentStatusColors[entity.status_id]}>{paymentStatuses[entity.status_id]}</Badge>))

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'amount':
            return <FormatMoney
                customers={props.customers} customer_id={entity.customer_id}
                amount={entity.amount}/>
        case 'applied':
            return <FormatMoney
                customers={props.customers} customer_id={entity.customer_id}
                amount={entity.applied}/>
        case 'date':
        case 'created_at': {
            return <FormatDate field={field} date={entity[field]}/>
        }

        case 'status_field':
            return status

        case 'status_id':
            return status

        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return customer.name
        }

        case 'invoices': {
            const invoices = paymentModel.paymentableInvoices
            return invoices && invoices.length > 0 ? Array.prototype.map.call(invoices, s => s.number).toString() : null
        }

        case 'credits': {
            const credits = paymentModel.paymentableCredits
            return credits && credits.length > 0 ? Array.prototype.map.call(credits, s => s.number).toString() : null
        }

        default:
            return entity[field]
    }
}
