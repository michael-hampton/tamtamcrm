import { Badge } from 'reactstrap'
import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import { expenseStatusColors, expenseStatuses, frequencyOptions } from '../utils/_consts'
import { translations } from '../utils/_translations'
import { contrast } from '../utils/_colors'
import ExpenseModel from '../models/ExpenseModel'

export function getDefaultTableFields () {
    return [
        'number',
        'status_id',
        'company_id',
        'customer_id',
        'date',
        'amount'
    ]
}

export default function ExpensePresenter (props) {
    const { field, entity } = props

    const color = entity.category && entity.category.column_color && entity.category.column_color.length ? entity.category.column_color : ''
    const status_chip = color.length ? <span className="badge" style={{
        backgroundColor: color,
        color: contrast(color)
    }}>{expenseStatuses[entity.status_id]}</span>
        : <Badge color={expenseStatusColors[entity.status_id]}>{expenseStatuses[entity.status_id]}</Badge>

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        status_chip))

    const paymentInvoices = entity.invoices && Object.keys(entity.invoices).length > 0 ? Array.prototype.map.call(entity.invoices, s => s.number).toString() : null

    const expenseModel = new ExpenseModel(entity, props.customers)
    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'frequency':
            return translations[frequencyOptions[entity.frequency]]
        case 'net_amount':
            return <FormatMoney
                customers={props.customers} customer_id={entity.customer_id}
                amount={expenseModel.netAmount}/>
        case 'amount':
            return <FormatMoney
                customers={props.customers} customer_id={entity.customer_id}
                amount={expenseModel.grossAmount}/>
        case 'tax_amount':
            return <FormatMoney
                customers={props.customers} customer_id={entity.customer_id}
                amount={expenseModel.amountWithTax}/>
        case 'status_field':
            return status
        case 'date':
        case 'created_at':
        case 'payment_date': {
            return !entity[field] || !entity[field].length || entity[field] === '0000-00-00' ? ''
                : <FormatDate field={field} date={entity[field]}/>
        }

        case 'status_id':
            return status

        case 'customer_id': {
            if (!entity[field]) {
                return ''
            }

            const customerIndex = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[customerIndex]
            return customer.name
        }

        case 'company_id': {
            if (!entity.company_id) {
                return ''
            }

            const companyIndex = props.companies.findIndex(company => company.id === entity[field])
            const company = props.companies[companyIndex]
            return company.name
        }

        case 'invoices':
            return paymentInvoices

        default:
            return entity[field]
    }
}
