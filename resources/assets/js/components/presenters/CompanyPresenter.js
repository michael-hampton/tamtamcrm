import React from 'react'

export function getDefaultTableFields () {
    return [
        'number',
        'name',
        'phone_number',
        'email',
        'website',
        'logo'
    ]
}

export default function CompanyPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'assigned_to': {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
            return assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
        }
        case 'user_id': {
            const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.user_id))
            return `${user[0].first_name} ${user[0].last_name}`
        }
        case 'logo':
            return <img style={{ width: '100px' }} src={entity.logo}/>
        default:
            return entity[field]
    }
}
