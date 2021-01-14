import React from 'react'
import { translations } from '../../utils/_translations'
import FormBuilder from '../FormBuilder'

export default function Step1 (props) {
    const settings = props.settings

    const formFields = [
        [
            {
                name: 'name',
                label: translations.name,
                type: 'text',
                placeholder: translations.name,
                value: settings.name,
                group: 1
            },
            {
                name: 'email',
                label: translations.email,
                type: 'text',
                placeholder: translations.email,
                value: settings.email,
                group: 1
            },
            {
                name: 'currency_id',
                label: translations.currency,
                type: 'currency',
                placeholder: translations.currency,
                value: settings.currency_id,
                group: 3
            },
            {
                name: 'language_id',
                label: translations.language,
                type: 'language',
                placeholder: translations.language,
                value: settings.language_id,
                group: 3
            }
        ]
    ]

    if (props.currentStep !== 1) {
        return null
    }
    return <FormBuilder
        handleChange={props.handleSettingsChange}
        formFieldsRows={formFields}
    />
}
