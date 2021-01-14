import React, { Component } from 'react'
import FormBuilder from '../../settings/FormBuilder'
import { translations } from '../../utils/_translations'
import { consts } from '../../utils/_consts'

export default class Config extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            settings: {}
        }
    }

    getCustomConfig () {
        return []
    }

    getAuthorizeConfig () {
        const settings = this.props.gateway.config

        const formFields = [
            [
                {
                    name: 'apiLoginId',
                    label: translations.api_login_id,
                    type: 'text',
                    placeholder: 'Api Login ID',
                    value: settings.apiLoginId
                },
                {
                    name: 'transactionKey',
                    label: translations.transaction_key,
                    type: 'password',
                    placeholder: 'Transaction Key',
                    value: settings.transactionKey
                },
                {
                    name: 'mode',
                    label: translations.mode,
                    type: 'select',
                    placeholder: 'Transaction Key',
                    value: settings.mode || '',
                    options: [
                        {
                            value: consts.gateway_mode_live,
                            text: translations.live
                        },
                        {
                            value: consts.gateway_mode_production,
                            text: translations.production
                        }
                    ]
                },
                {
                    name: 'live_url',
                    label: translations.live_url,
                    type: 'text',
                    placeholder: translations.live_url,
                    value: settings.live_url || 'https://api2.authorize.net/xml/v1/request.api'
                },
                {
                    name: 'production_url',
                    label: translations.production_url,
                    type: 'text',
                    placeholder: translations.production_url,
                    value: settings.production_url || 'https://apitest.authorize.net/xml/v1/request.api'
                }
            ]
        ]

        return formFields
    }

    getPaypalConfig () {
        const settings = this.props.gateway.config

        const formFields = [
            [
                {
                    name: 'password',
                    label: translations.password,
                    type: 'password',
                    placeholder: translations.password,
                    value: settings.password
                },
                {
                    name: 'signature',
                    label: 'Signature',
                    type: 'text',
                    placeholder: 'Signature',
                    value: settings.signature
                },
                {
                    name: 'username',
                    label: translations.username,
                    type: 'text',
                    placeholder: translations.username,
                    value: settings.username
                },
                {
                    name: 'mode',
                    label: translations.mode,
                    type: 'select',
                    value: settings.mode || '',
                    options: [
                        {
                            value: consts.gateway_mode_live,
                            text: translations.live
                        },
                        {
                            value: consts.gateway_mode_production,
                            text: translations.production
                        }
                    ]
                },
                {
                    name: 'live_url',
                    label: translations.live_url,
                    type: 'text',
                    placeholder: translations.live_url,
                    value: settings.live_url || ''
                },
                {
                    name: 'production_url',
                    label: translations.production_url,
                    type: 'text',
                    placeholder: translations.production_url,
                    value: settings.production_url || ''
                }
            ]
        ]

        return formFields
    }

    getStripeConfig () {
        const settings = this.props.gateway.config

        const formFields = [
            [
                {
                    name: 'apiKey',
                    label: translations.secret_key,
                    type: 'password',
                    placeholder: 'Secret Key',
                    value: settings.apiKey
                },
                {
                    name: 'publishable_key',
                    label: translations.publishable_key,
                    type: 'password',
                    placeholder: 'Publishable Key',
                    value: settings.publishable_key
                },
                {
                    name: 'mode',
                    label: translations.mode,
                    type: 'select',
                    placeholder: 'Transaction Key',
                    value: settings.mode || '',
                    options: [
                        {
                            value: consts.gateway_mode_live,
                            text: translations.live
                        },
                        {
                            value: consts.gateway_mode_production,
                            text: translations.production
                        }
                    ]
                },
                {
                    name: 'live_url',
                    label: translations.live_url,
                    type: 'text',
                    placeholder: translations.live_url,
                    value: settings.live_url || ''
                },
                {
                    name: 'production_url',
                    label: translations.production_url,
                    type: 'text',
                    placeholder: translations.production_url,
                    value: settings.production_url || ''
                }
            ]
        ]

        return formFields
    }

    getFormFields (key) {
        switch (key) {
            case '8ab2dce2':
                return this.getAuthorizeConfig()
            case '64bcbdce':
                return this.getPaypalConfig()
            case '13bb8d58':
                return this.getStripeConfig()
        }
    }

    render () {
        const formFields = this.props.gateway.gateway_key && this.props.gateway.gateway_key.length ? this.getFormFields(this.props.gateway.gateway_key) : null

        return formFields && formFields.length ? <FormBuilder
            handleChange={this.props.handleConfig}
            formFieldsRows={formFields}
        /> : null
    }
}
