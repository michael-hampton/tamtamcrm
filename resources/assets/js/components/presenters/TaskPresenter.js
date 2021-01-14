import { Badge } from 'reactstrap'
import React from 'react'
import ViewTask from '../tasks/ViewTask'
import FormatDate from '../common/FormatDate'
import { translations } from '../utils/_translations'
import { frequencyOptions } from '../utils/_consts'
import { contrast } from '../utils/_colors'

export function getDefaultTableFields () {
    return [
        'number',
        'customer_id',
        'due_date',
        'name',
        'description',
        'status_name',
        'duration'
    ]
}

export default function TaskPresenter (props) {
    const { field, entity } = props

    const color = entity.task_status && entity.task_status.column_color && entity.task_status.column_color.length ? entity.task_status.column_color : '#20a8d8'

    const status = (entity.deleted_at)
        ? (<Badge color="warning">{translations.archived}</Badge>)
        : ((entity.invoice_id) ? (<Badge color="success">{translations.invoiced}</Badge>)
            : (<span style={{ backgroundColor: color, color: contrast(color) }}
                className="badge">{entity.status_name.length ? entity.status_name : translations.logged}</span>))

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'status_name':
            return status
        case 'status_field':
            return status
        case 'frequency':
            return translations[frequencyOptions[entity.frequency]]
        case 'due_date':
        case 'start_date':
        case 'created_at':
            return <FormatDate date={entity[field]}/>
        case 'customer_id': {
            if (!entity[field]) {
                return ''
            }

            const customerIndex = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[customerIndex]
            return customer.name
        }
        case 'title':
            return <ViewTask custom_fields={props.custom_fields}
                project_id={props.task.project_id}
                users={props.users}
                customers={props.customers}
                task_type={1}
                allTasks={props.tasks}
                action={props.action}
                task={props.task}/>
        case 'project':
            return props.entity.project && props.entity.project.name ? props.entity.project.name : ''
        default:
            return entity[field]
    }
}
