import { Badge } from 'reactstrap'
import React from 'react'
import { translations } from '../utils/_translations'
import FormatMoney from '../common/FormatMoney'

export function getDefaultTableFields () {
    return [
        'name',
        'description',
        'price',
        'sku',
        'is_featured'
    ]
}

export default function ProductPresenter (props) {
    const { field, entity } = props

    const status = entity.deleted_at ? <Badge className="mr-2" color="warning">{translations.archived}</Badge> : null

    switch (field) {
        case 'price':
            return <FormatMoney amount={entity[field]}/>
        case 'status_field':
            return status
        case 'status_id':
            return status
        case 'company_id': {
            const index = props.companies.findIndex(company => company.id === entity[field])
            const company = props.companies[index]
            return company.name
        }
        case 'is_featured': {
            const icon = parseInt(entity.is_featured) === 1 ? 'fa-check' : 'fa-times-circle'
            const icon_class = parseInt(entity.is_featured) === 1 ? 'text-success' : 'text-danger'
            return <i className={`fa ${icon} ${icon_class}`}/>
        }
        default:
            return entity[field]
    }
}
