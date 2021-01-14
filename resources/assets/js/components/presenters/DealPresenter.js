import React from 'react'
import FormatDate from '../common/FormatDate'
import { Badge } from 'reactstrap'
import { translations } from '../utils/_translations'
import { contrast } from '../utils/_colors'

export function getDefaultTableFields () {
    return [
        'number',
        'name',
        'description',
        'due_date',
        'status_name'
    ]
}

export default function DealPresenter (props) {
    const { field, entity } = props

    const color = entity.task_status && entity.task_status.column_color && entity.task_status.column_color.length ? entity.task_status.column_color : '#20a8d8'

    const status = (entity.deleted_at) ? <Badge color="warning">{translations.archived}</Badge>
        : <span style={{ backgroundColor: color, color: contrast(color) }}
            className="badge">{entity.status_name}</span>

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'status_name':
        case 'status_field':
            return status
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'due_date':
            return <FormatDate date={entity[field]}/>
        case 'customer_id': {
            if (!entity[field]) {
                return ''
            }

            const customerIndex = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[customerIndex]
            return customer.name
        }
        case 'project':
            return props.entity.project && props.entity.project.name ? props.entity.project.name : ''
        default:
            return entity[field]
    }
}
