import React from 'react'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'name',
        'description',
        'send_on'
    ]
}

export default function BankAccountPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'bank':
            return entity[field].name
        default:
            return entity[field]
    }
}
