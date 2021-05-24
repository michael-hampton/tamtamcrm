import React from 'react'
import { Card, CardBody, CardHeader, CustomInput, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import QuoteDropdown from '../../common/dropdowns/QuoteDropdown'
import SwitchWithIcon from "../../common/SwitchWithIcon";

export default function Details (props) {
    return (
        <Card>
            <CardHeader>{translations.details}</CardHeader>
            <CardBody>
                {!!props.show_quote &&
                <FormGroup>
                    <Label>{translations.quote}</Label>
                    <QuoteDropdown
                        is_recurring={true}
                        quotes={props.allQuotes}
                        handleInputChanges={props.handleInput}
                        name="quote_id"
                        errors={props.errors}
                    />
                </FormGroup>
                }

                <FormGroup>
                    <Label>{translations.number}</Label>
                    <Input className={props.hasErrorFor('number') ? 'form-control is-invalid' : 'form-control'}
                        value={props.recurring_quote.number}
                        type='text'
                        name='number'
                        id='number'
                        onChange={props.handleInput}
                    />
                    {props.renderErrorFor('number')}
                </FormGroup>

                <FormGroup>
                    <Label for="po_number">{translations.po_number}(*):</Label>
                    <Input value={props.recurring_quote.po_number} type="text" id="po_number" name="po_number"
                        onChange={props.handleInput}/>
                    {props.renderErrorFor('po_number')}
                </FormGroup>

                <SwitchWithIcon
                    label={translations.auto_billing_enabled}
                    icon={icons.credit_card}
                    checked={props.recurring_quote.auto_billing_enabled}
                    name="auto_billing_enabled"
                    handleInput={props.handleInput}
                    help_text={translations.auto_billing_enabled_help_text}
                />
            </CardBody>
        </Card>

    )
}
