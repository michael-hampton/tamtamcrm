import React from 'react'
import { Card, CardBody, CardHeader, Col, FormGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import FormBuilder from '../../settings/FormBuilder'
import Checkbox from '../../common/Checkbox'
import { consts } from '../../utils/_consts'
import { icons } from '../../utils/_icons'
import SwitchWithIcon from '../../common/SwitchWithIcon'

export default class Settings extends React.Component {
    constructor (props) {
        super(props)

        this.card_types = [
            {
                name: 'visa',
                label: 'Visa'
            },
            {
                name: 'mastercard',
                label: 'Mastercard'
            },
            {
                name: 'american_express',
                label: 'American Express'
            },
            {
                name: 'diners_card',
                label: 'Diners Card'
            },
            {
                name: 'discover_card',
                label: 'Discover Card'
            }
        ]

        this.fields = [
            {
                name: 'email_required',
                label: 'Email Required'
            },
            {
                name: 'phone_required',
                label: 'Phone Required'
            },
            {
                name: 'shipping_required',
                label: 'Shipping Required',
                help_text: translations.show_shipping_address_help_text
            },
            {
                name: 'billing_required',
                label: 'Billing Required',
                help_text: translations.show_billing_address_help_text
            },
            {
                name: 'display_email',
                label: 'Display Email'
            },
            {
                name: 'display_phone',
                label: 'Display Phone'
            },
            {
                name: 'display_billing',
                label: 'Display Billing'
            },
            {
                name: 'display_shipping',
                label: 'Display Shipping'
            }
        ]
    }

    getSettingFields () {
        const settings = this.props.gateway
        const supports_token_billing = [consts.stripe_gateway, consts.authorize_gateway].includes(this.props.gateway.gateway_key) || false
        const fields = [
            {
                name: 'require_cvv',
                label: translations.require_cvv,
                type: 'switch',
                placeholder: translations.require_cvv,
                value: settings.require_cvv ? settings.require_cvv : '',
                group: 1,
                class_name: 'col-12'
            }
            // {
            //     name: 'update_details',
            //     label: translations.update_details,
            //     help_text: translations.update_details_help_text,
            //     type: 'switch',
            //     placeholder: translations.update_details,
            //     value: settings.update_details ? settings.update_details : '',
            //     group: 1,
            //     class_name: 'col-12'
            // }
        ]

        if (supports_token_billing) {
            fields.push(
                {
                    name: 'should_store_card',
                    label: translations.store_card,
                    type: 'switch',
                    placeholder: translations.store_card,
                    value: settings.should_store_card,
                    class_name: 'col-12'
                }
            )
        }

        return [fields]
    }

    render () {
        const gateway = this.props.gateway.gateway_key.length ? JSON.parse(localStorage.getItem('gateways')).filter(gateway => gateway.key === this.props.gateway.gateway_key) : []
        const is_offsite = gateway.length && parseInt(gateway[0].offsite_only) === 1

        console.log('gateway', this.props.gateway)

        return <React.Fragment>
            <Card>
                <CardHeader>{translations.settings}</CardHeader>
                <CardBody>
                    <FormBuilder
                        handleCheckboxChange={this.props.handleInput}
                        handleChange={this.props.handleInput}
                        formFieldsRows={this.getSettingFields()}
                    />
                </CardBody>
            </Card>

            {!is_offsite &&
            <Card>
                <CardHeader>{translations.fields}</CardHeader>
                <CardBody>
                    <FormGroup>
                        <Row>
                            <Col sm={10}>
                                {
                                    this.fields.map((item, index) => (
                                        <SwitchWithIcon
                                            icon={icons.customer}
                                            label={item.label}
                                            checked={this.props.gateway.required_fields.get(item.name)}
                                            name={item.name}
                                            handleInput={this.props.updateFields}
                                            help_text={item.help_text}/>
                                    ))
                                }
                            </Col>
                        </Row>
                    </FormGroup>

                </CardBody>
            </Card>
            }

            {!is_offsite &&
            <Card>
                <CardHeader>{translations.accepted_cards}</CardHeader>
                <CardBody>
                    <FormGroup>
                        <Row>
                            <Col sm={10}>
                                {
                                    this.card_types.map((item, index) => (
                                        <div key={index} className="form-check">
                                            <Checkbox name={item.name}
                                                checked={this.props.gateway.accepted_cards.get(item.name)}
                                                onChange={this.props.updateCards}/>
                                            <label className="form-check-label" htmlFor="gridRadios1">
                                                {item.label}
                                            </label>
                                        </div>
                                    ))
                                }
                            </Col>
                        </Row>
                    </FormGroup>

                </CardBody>
            </Card>
            }

        </React.Fragment>
    }
}
