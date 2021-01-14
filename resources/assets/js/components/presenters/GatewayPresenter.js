import React from 'react'

export default function GatewayPresenter (props) {
    const { field, entity } = props

    switch (field) {
        default:
            return <td onClick={() => props.toggleViewedEntity(entity, `Gateway ${entity.gateway.name}`)} key={field}
                data-label={field}>{entity[field]}</td>
    }
}
