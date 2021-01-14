import React from 'react'
import FormatDate from '../common/FormatDate'

export function getDefaultTableFields () {
    return [
        'name',
        'rate'
    ]
}

export default function TaxRatePresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'due_date':
            return <FormatDate date={entity[field]}/>
        case 'rate':
            return `${parseFloat(entity[field]).toFixed(2)}%`
        default:
            return entity[field]
    }
}
