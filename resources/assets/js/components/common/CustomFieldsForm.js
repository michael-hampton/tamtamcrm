import React from 'react'
import { Card, CardBody, CardHeader } from 'reactstrap'
import FormBuilder from '../settings/FormBuilder'
import { translations } from '../utils/_translations'

export default function CustomFieldsForm (props) {
    const customFields = props.custom_fields ? props.custom_fields : []
    let has_custom_field = false

    if (customFields[0] && Object.keys(customFields[0]).length) {
        customFields[0].forEach((element, index, array) => {
            customFields[0][index].value = props[element.name] && props[element.name].toString().length ? props[element.name] : ''
        })

        has_custom_field = customFields[0].filter(field => field.label.length && field.type.length).length
    }

    return has_custom_field
        ? <Card>
            <CardHeader>{translations.custom_fields}</CardHeader>
            <CardBody>
                <FormBuilder
                    handleChange={props.handleInput.bind(this)}
                    formFieldsRows={customFields}
                />
            </CardBody>
        </Card> : null
}
