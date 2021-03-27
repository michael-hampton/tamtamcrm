import React from 'react'
import FormatDate from '../common/FormatDate'

export function getDefaultTableFields () {
    return [
        'plan_name',
        'name',
        'starts_at',
        'ends_at',
        'due_date',
        'trial_ends_at',
        'number_of_licences'
    ]
}

export default function PlanPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'plan_name':
            return entity.plan_name
        case 'due_date':
        case 'starts_at':
        case 'ends_at':
        case 'trial_ends_at':
        case 'cancelled_at':
            return <FormatDate date={entity[field]}/>
        default:
            return typeof entity[field] === 'object' ? JSON.stringify(entity[field]) : entity[field]
    }
}
