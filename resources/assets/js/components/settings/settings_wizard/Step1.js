import React from 'react'
import { translations } from '../../utils/_translations'
import FormBuilder from '../FormBuilder'

export default function Step1 (props) {
    const settings = props.settings

    let icon = null

    if (props.checking === true) {
        icon = 'fa-ban'
    } else {
        icon = props.domain_valid === true ? 'fa-check-circle' : 'fa-exclamation-circle'
    }

    const formFields = [
        [
            {
                name: 'first_name',
                label: translations.first_name,
                type: 'text',
                placeholder: translations.first_name,
                // value: settings.name,
                group: 1
            },
            {
                name: 'last_name',
                label: translations.last_name,
                type: 'text',
                placeholder: translations.last_name,
                // value: settings.name,
                group: 1
            },
            {
                name: 'subdomain',
                label: translations.subdomain,
                type: 'input_group',
                placeholder: translations.subdomain,
                icon: icon,
                onClick: props.checkDomain,
                // value: settings.name,
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
                name: 'password',
                label: translations.password,
                type: 'password',
                placeholder: translations.password,
                // value: settings.email,
                group: 1
            },
            {
                name: 'confirm_password',
                label: translations.confirm_password,
                type: 'password',
                placeholder: translations.confirm_password,
                // value: settings.email,
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
        handleChange={props.handleChange}
        formFieldsRows={formFields}
    />
}
