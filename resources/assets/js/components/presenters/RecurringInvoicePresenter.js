import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import { frequencyOptions, recurringInvoiceStatusColors, recurringInvoiceStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'number',
        'customer_id',
        'date',
        'due_date',
        'total',
        'balance',
        'status_id',
        'invoices',
        'frequency',
        'date_to_send',
        'number_of_occurances'
    ]
}

export default function RecurringInvoicePresenter (props) {
    const { field, entity } = props

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        <Badge
            color={recurringInvoiceStatusColors[entity.status_id]}>{recurringInvoiceStatuses[entity.status_id]}</Badge>))

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'number_of_occurrances':
            return entity.is_never_ending ? translations.never_ending : entity.number_of_occurrances
        case 'frequency':
            return translations[frequencyOptions[entity.frequency]]
        case 'exchange_rate':
        case 'balance':
        case 'total':
        case 'discount_total':
        case 'tax_total':
        case 'sub_total':
            return <FormatMoney customer_id={entity.customer_id} customers={props.customers} amount={entity[field]}/>
        case 'date':
        case 'due_date':
        case 'created_at':
        case 'start_date':
        case 'last_sent_date':
        case 'date_to_send':
        case 'expiry_date': {
            return <FormatDate
                field={field} date={entity[field]}/>
        }

        case 'auto_billing_enabled':
            return entity[field] === true ? translations.yes : translations.no

        case 'status_field':
            return status
        case 'status_id':
            return status

        case 'customer_id': {
            const customerIndex = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[customerIndex]
            return customer.name
        }

        case 'currency_id': {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(props.entity.currency_id))
            return currency.length ? currency[0].iso_code : ''
        }

        case 'invoices': {
            const invoices = entity.invoices
            return invoices && invoices.length > 0 ? Array.prototype.map.call(invoices, s => s.number).toString() : null
        }

        default:
            return entity[field] || ''
    }
}
