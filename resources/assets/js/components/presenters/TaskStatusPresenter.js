import React from 'react'

export function getDefaultTableFields () {
    return [
        'name',
        'description',
        'task_count'
    ]
}

export default function TaskStatusPresenter (props) {
    const { field, entity } = props

    switch (field) {
        default:
            return typeof entity[field] === 'object' ? JSON.stringify(entity[field]) : entity[field]
    }
}
