import React from 'react'

export function getDefaultTableFields () {
    return [
        'name'
    ]
}

export default function CategoryPresenter (props) {
    const { field, entity } = props

    switch (field) {
        default:
            return entity[field]
    }
}
