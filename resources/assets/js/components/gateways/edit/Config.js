import React, {Component} from 'react'
import FormBuilder from '../../settings/FormBuilder'
import {translations} from '../../utils/_translations'
import {consts} from '../../utils/_consts'
import {Button} from 'reactstrap'
import CompanyGatewayRepository from '../../repositories/CompanyGatewayRepository'

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
        const settings = this.props.gateway.settings

        return [
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
                    name: 'testMode',
                    label: translations.mode,
                    type: 'switch',
                    placeholder: translations.mode,
                    value: settings.mode ? settings.mode : false,
                    group: 1,
                    //class_name: 'col-12'
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
    }

    getBraintreeConfig () {
        const settings = this.props.gateway.settings

        return [
            [
                {
                    name: 'merchant_id',
                    label: translations.merchant_id,
                    type: 'text',
                    value: settings.merchant_id
                },
                {
                    name: 'public_key',
                    label: translations.public_key,
                    type: 'password',
                    value: settings.public_key
                },
                {
                    name: 'private_key',
                    label: translations.private_key,
                    type: 'password',
                    value: settings.private_key
                },
                {
                    name: 'testMode',
                    label: translations.mode,
                    type: 'switch',
                    placeholder: translations.mode,
                    value: settings.mode ? settings.mode : false,
                    group: 1,
                    //class_name: 'col-12'
                },
                // {
                //     name: 'live_url',
                //     label: translations.live_url,
                //     type: 'text',
                //     placeholder: translations.live_url,
                //     value: settings.live_url || 'https://api2.authorize.net/xml/v1/request.api'
                // },
                // {
                //     name: 'production_url',
                //     label: translations.production_url,
                //     type: 'text',
                //     placeholder: translations.production_url,
                //     value: settings.production_url || 'https://apitest.authorize.net/xml/v1/request.api'
                // }
            ]
        ]
    }

    getCheckoutConfig () {
        const settings = this.props.gateway.settings

        return [
            [
                {
                    name: 'publicApiKey',
                    label: translations.public_key,
                    type: 'password',
                    value: settings.publicApiKey
                },
                {
                    name: 'secretApiKey',
                    label: translations.secret_key,
                    type: 'password',
                    value: settings.secretApiKey
                },
                {
                    name: 'testMode',
                    label: translations.mode,
                    type: 'switch',
                    placeholder: translations.mode,
                    value: settings.mode ? settings.mode : false,
                    group: 1,
                    //class_name: 'col-12'
                },
            ]
        ]
    }

    getPaypalConfig () {
        const settings = this.props.gateway.settings

        return [
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
    }

    getStripeConfig () {
        const settings = this.props.gateway.settings

        return [
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
                    name: 'testMode',
                    label: translations.mode,
                    type: 'switch',
                    placeholder: translations.mode,
                    value: settings.mode ? settings.mode : false,
                    group: 1,
                    //class_name: 'col-12'
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
    }

    createStripeAccount (key) {
        const companyGatewayRepository = new CompanyGatewayRepository()
        companyGatewayRepository.createStripeAccount(key).then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            if (this.props.refresh) {
                this.props.refresh(response.gateway)
            }

            window.open(
                response.url,
                '_blank'
            )
        })
    }

    getFormFields (key) {
        switch (key) {
            case consts.authorize_gateway:
                return this.getAuthorizeConfig()
            case consts.paypal_gateway:
                return this.getPaypalConfig()
            case consts.stripe_gateway:
                return this.getStripeConfig()
            case consts.braintree_gateway:
                return this.getBraintreeConfig()
            case consts.checkout_gateway:
                return this.getCheckoutConfig()
        }
    }

    render () {
        const formFields = this.props.gateway.gateway_key && this.props.gateway.gateway_key.length ? this.getFormFields(this.props.gateway.gateway_key) : null

        if (this.props.gateway.gateway_key === consts.stripe_connect_gateway && this.props.is_add === true) {
            return <Button color="primary" onClick={(e) => {
                this.createStripeAccount(consts.stripe_connect_gateway)
            }}>{translations.stripe_connect}</Button>
        }

        return formFields && formFields.length ? <FormBuilder
            handleChange={this.props.handleConfig}
            formFieldsRows={formFields}
        /> : null
    }
}
