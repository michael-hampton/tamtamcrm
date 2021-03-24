import React from 'react'
import FormatDate from '../common/FormatDate'

export function getDefaultTableFields () {
    return [
        'name',
        'starts_at',
        'ends_at',
        'due_date',
        'trial_ends_at'
    ]
}

export default function PlanPresenter (props) {
    const { field, entity } = props

    switch (field) {
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
