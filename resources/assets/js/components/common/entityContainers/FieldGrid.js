import React from 'react'
import { Row } from 'reactstrap'
import SimpleSectionItem from './SimpleSectionItem'
import { translations } from '../../utils/_translations'

export default function FieldGrid (props) {
    const fieldWidgets = []

    Object.keys(props.fields).map(field => {
        if (props.fields[field] != null && (props.fields[field].length || Object.keys(props.fields[field]).length)) {
            const label = translations[field] || field
            fieldWidgets.push(<SimpleSectionItem custom_class="border-0" heading={label}
                value={props.fields[field]}/>)
        }
    })

    return <Row>
        <ul className="mt-4 col-12 p-0">
            {fieldWidgets}
        </ul>
    </Row>
}
