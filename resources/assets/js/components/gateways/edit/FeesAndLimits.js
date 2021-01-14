import React from 'react'
import { Card, CardBody, FormGroup, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import FormBuilder from '../../settings/FormBuilder'
import TaxRateDropdown from '../../common/dropdowns/TaxRateDropdown'

export default class FeesAndLimits extends React.Component {
    constructor (props) {
        super(props)

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    getSettingFieldsSectionOne () {
        const settings = this.props.gateway.fees_and_limits.length ? this.props.gateway.fees_and_limits[0] : ''

        return [
            [
                {
                    name: 'min_limit',
                    label: translations.min_limit,
                    type: 'text',
                    placeholder: translations.min_limit,
                    value: settings && settings.min_limit ? settings.min_limit : '',
                    group: 1
                },
                {
                    name: 'max_limit',
                    label: translations.max_limit,
                    type: 'text',
                    placeholder: translations.max_limit,
                    value: settings && settings.max_limit ? settings.max_limit : '',
                    group: 1
                }
            ]
        ]
    }

    getSettingFieldsSectionTwo () {
        const settings = this.props.gateway.fees_and_limits.length ? this.props.gateway.fees_and_limits[0] : ''

        return [
            [
                {
                    name: 'fee_amount',
                    label: translations.fee_amount,
                    type: 'text',
                    placeholder: translations.fee_amount,
                    value: settings && settings.fee_amount ? settings.fee_amount : '',
                    group: 1
                },
                {
                    name: 'fee_percent',
                    label: translations.fee_percent,
                    type: 'text',
                    placeholder: translations.fee_percent,
                    value: settings && settings.fee_percent ? settings.fee_percent : '',
                    group: 1
                },
                {
                    name: 'fee_cap',
                    label: translations.fee_cap,
                    type: 'text',
                    placeholder: translations.fee_cap,
                    value: settings && settings.fee_cap ? settings.fee_cap : '',
                    group: 2
                }
            ]
        ]
    }

    render () {
        return <React.Fragment>
            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.props.updateFeesAndLimits}
                        formFieldsRows={this.getSettingFieldsSectionOne()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.props.updateFeesAndLimits}
                        formFieldsRows={this.getSettingFieldsSectionTwo()}
                    />

                    {this.settings.show_tax_rate1 &&
                    <FormGroup>
                        <Label>{translations.tax}</Label>
                        <TaxRateDropdown
                            name="tax"
                            handleInputChanges={this.props.updateFeesAndLimits}
                        />
                    </FormGroup>
                    }

                    {this.settings.show_tax_rate2 &&
                    <FormGroup>
                        <Label>{translations.tax}</Label>
                        <TaxRateDropdown
                            name="tax_2"
                            handleInputChanges={this.props.updateFeesAndLimits}
                        />
                    </FormGroup>
                    }

                    {this.settings.show_tax_rate3 &&
                    <FormGroup>
                        <Label>{translations.tax}</Label>
                        <TaxRateDropdown
                            name="tax_3"
                            handleInputChanges={this.props.updateFeesAndLimits}
                        />
                    </FormGroup>
                    }
                </CardBody>
            </Card>
        </React.Fragment>
    }
}
