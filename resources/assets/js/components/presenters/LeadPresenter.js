import React from 'react'
import { Badge } from 'reactstrap'
import { translations } from '../utils/_translations'
import FormatMoney from '../common/FormatMoney'
import { contrast } from '../utils/_colors'

export function getDefaultTableFields () {
    return [
        'number',
        'first_name',
        'last_name',
        'status_name',
        'email',
        'phone',
        'status_id'
    ]
}

export default function LeadPresenter (props) {
    const { field, entity } = props

    const color = entity.task_status && entity.task_status.column_color && entity.task_status.column_color.length ? entity.task_status.column_color : '#20a8d8'

    const status = (entity.deleted_at) ? <Badge color="warning">{translations.archived}</Badge>
        : <span style={{ backgroundColor: color, color: contrast(color) }}
            className="badge">{entity.status_name.length ? entity.status_name : translations.logged}</span>

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'valued_at':
            return <FormatMoney amount={entity[field]}/>
        case 'status_name':
        case 'status_field':
            return status
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'name': {
            return `${entity.first_name} ${entity.last_name}`
        }
        case 'project':
            return props.entity.project && props.entity.project.name ? props.entity.project.name : ''
        default:
            return entity[field]
    }
}
