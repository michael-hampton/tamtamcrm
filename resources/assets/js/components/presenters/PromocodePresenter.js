import React from 'react'
import FormatMoney from '../common/FormatMoney'
import FormatDate from '../common/FormatDate'

export default function PromocodePresenter (props) {
    const { field, entity } = props

    switch (field) {
        case 'customer_id': {
            const index = props.customers.findIndex(customer => customer.id === entity[field])
            const customer = props.customers[index]
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                data-label="Customer">{customer.name}</td>
        }
        case 'date':
        case 'expires_at':
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                data-label={field}>
                <FormatDate field={field} date={entity[field]}/></td>
        case 'reward':
        case 'scope_value':
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                data-label={field}>
                <FormatMoney customer_id={entity.customer_id} customers={props.customers} amount={entity[field]}/>
            </td>
        case 'amount_type': {
            const icon = entity.amount_type === 'pct' ? 'fa-percent' : 'fa-gbp'
            return <td className="text-center"
                onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)}
                key={field}
                data-label={field}><span className={`fa ${icon}`}/></td>
        }
        case 'is_disposable': {
            const icon = parseInt(entity.is_disposable) === 1 ? 'fa-check' : 'fa-times-circle'
            return <td className="text-center" onClick={() => props.toggleViewedEntity(entity, entity.number)}
                key={field}
                data-label={field}><i className={`fa ${icon}`}/></td>
        }
        default:
            return <td onClick={() => props.toggleViewedEntity(entity, entity.number, props.edit)} key={field}
                data-label={field}>{entity[field]}</td>
    }
}
