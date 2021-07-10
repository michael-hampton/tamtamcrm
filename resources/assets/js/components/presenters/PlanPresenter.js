import React from 'react'
import FormatDate from '../common/FormatDate'
import FormatMoney from '../common/FormatMoney'

export function getDefaultTableFields () {
    return [
        'name',
        'description',
        'code',
        'trial_period',
        'price',
        'active_subscribers_limit'
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
        case 'price':
            return <FormatMoney amount={entity.price} />
        default:
            return typeof entity[field] === 'object' ? JSON.stringify(entity[field]) : entity[field]
    }
}
