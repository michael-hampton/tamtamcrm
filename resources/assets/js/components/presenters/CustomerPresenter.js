import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'
import Avatar from '../common/Avatar'

export function getDefaultTableFields () {
    return [
        'number',
        'name',
        'phone',
        'email',
        'website',
        'balance',
        'amount_paid'
    ]
}

export default function CustomerPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'industry_id': {
            const industry = JSON.parse(localStorage.getItem('industries')).filter(user => user.id === parseInt(props.entity.industry_id))
            return industry && industry.length ? industry[0].name : ''
        }

        case 'language_id': {
            const language = JSON.parse(localStorage.getItem('languages')).filter(user => user.id === parseInt(props.entity.language_id))
            return language && language.length ? language[0].name : ''
        }

        case 'currency_id': {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(user => user.id === parseInt(props.entity.currency_id))
            return currency && currency.length ? currency[0].name : ''
        }

        case 'country_id': {
            const country = JSON.parse(localStorage.getItem('countries')).filter(user => user.id === parseInt(props.entity.country_id))

            return country && country.length ? country[0].name : ''
        }
        case 'id':
            return <Avatar name={entity.name}/>
        case 'date':
        case 'due_date':
        case 'created_at':
            return <FormatDate field={field} date={entity[field]}/>
        case 'balance': {
            const text_color = entity[field] <= 0 ? 'text-danger' : 'text-success'
            return <FormatMoney customer_id={entity.customer_id} className={text_color} customers={props.customers}
                amount={entity[field]}/>
        }
        case 'amount_paid':
            return <FormatMoney customer_id={entity.id} customers={props.customers} amount={entity[field]}/>
        default:
            return typeof entity[field] === 'object' || typeof entity[field] === 'array' ? JSON.stringify(entity[field]) : entity[field]
    }
}
