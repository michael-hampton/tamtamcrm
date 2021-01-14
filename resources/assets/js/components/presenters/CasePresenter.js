import { Badge } from 'reactstrap'
import React from 'react'
import { caseLinkTypes, casePriorities, casePriorityColors, caseStatusColors, caseStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'
import FormatDate from '../common/FormatDate'
import { contrast } from '../utils/_colors'

export function getDefaultTableFields () {
    return [
        'number',
        'customer_id',
        'subject',
        'due_date',
        'priority_id',
        'status_id'
    ]
}

export default function CasePresenter (props) {
    const { field, entity } = props

    const color = entity.category && entity.category.column_color && entity.category.column_color.length ? entity.category.column_color : ''

    const status_chip = color.length ? <span className="badge" style={{
        backgroundColor: color,
        color: contrast(color)
    }}>{caseStatuses[entity.status_id]}</span>
        : <Badge color={caseStatusColors[entity.status_id]}>{caseStatuses[entity.status_id]}</Badge>

    const status = (entity.deleted_at && !entity.is_deleted) ? (<Badge className="mr-2"
        color="warning">{translations.archived}</Badge>) : ((entity.deleted_at && entity.is_deleted) ? (
        <Badge className="mr-2" color="danger">{translations.deleted}</Badge>) : (
        status_chip))

    const priority = <Badge
        color={casePriorityColors[entity.priority_id]}>{casePriorities[entity.priority_id]}</Badge>

    switch (field) {
        case 'status_field':
            return status
        case 'priority_field':
            return priority
        case 'link_type':
            return entity.link_type.toString().length ? caseLinkTypes[entity.link_type] : ''

        case 'status_id':
            return status
        case 'priority_id':
            return priority
        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return customer.name
        }
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'date':
        case 'due_date':
        case 'created_at':
            return <FormatDate field={field} date={entity[field]}/>
        default:
            return entity[field]
    }
}
