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
        'paid_to_date'
    ]
}

export default function CustomerPresenter (props) {
    const { field, entity } = props

    switch (field) {
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
        case 'paid_to_date':
            return <FormatMoney customer_id={entity.id} customers={props.customers} amount={entity[field]}/>
        default:
            return entity[field]
    }
}
