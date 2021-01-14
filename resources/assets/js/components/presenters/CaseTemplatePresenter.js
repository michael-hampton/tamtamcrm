import React from 'react'
import { Badge } from 'reactstrap'
import { caseStatusColors, caseStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'

export default function CaseTemplatePresenter (props) {
    const { field, entity } = props

    const status = !entity.deleted_at
        ? <Badge color={caseStatusColors[entity.send_on]}>{caseStatuses[entity.send_on]}</Badge>
        : <Badge className="mr-2" color="warning">{translations.archived}</Badge>

    switch (field) {
        case 'send_on':
            return <td onClick={() => props.toggleViewedEntity(entity, entity.name, props.edit)}
                data-label="Send On">{status}</td>
        default:
            return <td onClick={() => props.toggleViewedEntity(entity, entity.name, props.edit)} key={field}
                data-label={field}>{entity[field]}</td>
    }
}
