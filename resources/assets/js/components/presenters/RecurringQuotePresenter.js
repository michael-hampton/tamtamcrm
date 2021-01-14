import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import { frequencyOptions, recurringQuoteStatusColors, recurringQuoteStatuses } from '../utils/_consts'
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
        'quotes',
        'frequency',
        'date_to_send',
        'number_of_occurances'
    ]
}

export default function RecurringQuotePresenter (props) {
    const { field, entity } = props

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        <Badge
            color={recurringQuoteStatusColors[entity.status_id]}>{recurringQuoteStatuses[entity.status_id]}</Badge>))

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
        case 'start_date':
        case 'created_at':
        case 'last_sent_date':
        case 'date_to_send':
        case 'expiry_date': {
            return <FormatDate
                field={field} date={entity[field]}/>
        }

        case 'status_field':
            return status
        case 'status_id':
            return status

        case 'auto_billing_enabled':
            return entity[field] === true ? translations.yes : translations.no

        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return customer.name
        }

        case 'currency_id': {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(props.entity.currency_id))
            return currency.length ? currency[0].iso_code : ''
        }

        case 'quotes': {
            const quotes = entity.quotes
            return quotes && quotes.length > 0 ? Array.prototype.map.call(quotes, s => s.number).toString() : null
        }

        default:
            return entity[field] || ''
    }
}
