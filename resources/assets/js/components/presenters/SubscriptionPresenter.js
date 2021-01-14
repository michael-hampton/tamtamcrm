import React from 'react'
import { subscriptions } from '../utils/_consts'
import { translations } from '../utils/_translations'

export function getDefaultTableFields () {
    return [
        'name',
        'target_url',
        'event_id',
        'format'
    ]
}

export default function SubscriptionPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'event_id': {
            let event = ''
            Object.keys(subscriptions).forEach(function (key) {
                if (subscriptions[key] === parseInt(entity.event_id)) {
                    event = key
                }
            })

            return event.length ? translations[event] : event
        }
        default:
            return entity[field]
    }
}
