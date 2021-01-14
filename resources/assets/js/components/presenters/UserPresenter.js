import React from 'react'

export function getDefaultTableFields () {
    return [
        'first_name',
        'last_name',
        'email',
        'phone_number'
    ]
}

export default function UserPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'name': {
            return `${entity.first_name} ${entity.last_name}`
        }

        default:
            return entity[field]
    }
}
