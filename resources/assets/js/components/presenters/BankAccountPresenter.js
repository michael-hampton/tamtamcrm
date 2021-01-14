import React from 'react'
import { translations } from '../utils/_translations'

export default function BankAccountPresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'bank':
            return <td onClick={() => props.toggleViewedEntity(entity, entity.name, props.edit)} key={field}
                data-label={translations.bank}>{entity[field].name}</td>
        default:
            return <td onClick={() => props.toggleViewedEntity(entity, entity.name, props.edit)} key={field}
                data-label={field}>{entity[field]}</td>
    }
}
