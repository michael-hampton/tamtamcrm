import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Label } from 'reactstrap'
import PaymentTypeDropdown from '../../common/dropdowns/PaymentTypeDropdown'
import CompanyDropdown from '../../common/dropdowns/CompanyDropdown'
import CurrencyDropdown from '../../common/dropdowns/CurrencyDropdown'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import GroupSettingsDropdown from '../../common/dropdowns/GroupSettingsDropdown'
import PaymentTermsDropdown from '../../common/dropdowns/PaymentTermsDropdown'
import { translations } from '../../utils/_translations'

export default function SettingsForm (props) {
    const hasErrorFor = (field) => {
        return props.errors && !!props.errors[field]
    }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    return (
        <Card>
            <CardHeader>Additional Info</CardHeader>
            <CardBody>

                <FormGroup>
                    <Label htmlFor="payment_terms"> Payment Terms </Label>
                    <PaymentTermsDropdown
                        name="payment_terms"
                        data-namespace="settings"
                        payment_term={props.settings.payment_terms}
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                    {renderErrorFor('payment_terms')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.payment_method}</Label>
                    <PaymentTypeDropdown
                        name="default_payment_method"
                        data-namespace="customer"
                        payment_type={props.customer.default_payment_method}
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.company}</Label>
                    <CompanyDropdown
                        data-namespace="customer"
                        company_id={props.customer.company_id}
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.currency}</Label>
                    <CurrencyDropdown
                        data-namespace="customer"
                        currency_id={props.customer.currency_id}
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.assigned_user}</Label>
                    <UserDropdown
                        data-namespace="customer"
                        user_id={props.customer.assigned_user}
                        name="assigned_user"
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.customer_group}</Label>
                    <GroupSettingsDropdown
                        data-namespace="customer"
                        group_settings_id={props.customer.group_settings_id}
                        name="group_settings_id"
                        errors={props.errors}
                        handleInputChanges={props.onChange}
                    />
                </FormGroup>

            </CardBody>
        </Card>

    )
}
