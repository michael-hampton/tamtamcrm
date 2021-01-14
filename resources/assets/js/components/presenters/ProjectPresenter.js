import React from 'react'
import FormatDate from '../common/FormatDate'

export function getDefaultTableFields () {
    return [
        'number',
        'name',
        'customer_id',
        'due_date',
        'budgeted_hours',
        'task_rate'
    ]
}

export default function ProjectPresenter (props) {
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
        case 'is_completed':
            return <i className="fa fa-check"/>
        case 'due_date':
        case 'created_at':
            return <FormatDate field={field} date={entity[field]}/>
        case 'customer_id': {
            if (!entity[field]) {
                return ''
            }

            const customerIndex = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[customerIndex]
            return customer.name
        }
        default:
            return entity[field]
    }
}
