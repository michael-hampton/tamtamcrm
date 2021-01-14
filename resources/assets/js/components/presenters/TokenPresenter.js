import React from 'react'

export function getDefaultTableFields () {
    return [
        'name',
        'user_id'
    ]
}

export default function TokenPresenter (props) {
    const { field, entity } = props
    const users = JSON.parse(localStorage.getItem('users'))
    const user = users.length ? users.filter(user => parseInt(user.id) === parseInt(entity.user_id)) : []

    switch (field) {
        case 'token':
            return <span>
                {`${entity.token.substring(0, 10)}xxxxxxxxxx`} <br/>
                {user.length &&
                `${user[0].first_name} ${user[0].last_name}`
                }
            </span>
        case 'user_id':
            return user.length ? `${user[0].first_name} ${user[0].last_name}` : ''
        default:
            return entity[field]
    }
}
