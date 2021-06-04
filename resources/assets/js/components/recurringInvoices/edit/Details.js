import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import InvoiceDropdown from '../../common/dropdowns/InvoiceDropdown'
import SwitchWithIcon from '../../common/SwitchWithIcon'

export default function Details (props) {
    return (
        <Card>
            <CardHeader>{translations.details}</CardHeader>
            <CardBody>
                {!!props.show_invoice &&
                <FormGroup>
                    <Label>{translations.invoice}</Label>
                    <InvoiceDropdown
                        is_recurring={true}
                        invoices={props.allInvoices}
                        handleInputChanges={props.handleInput}
                        name="invoice_id"
                        errors={props.errors}
                    />
                </FormGroup>
                }

                <FormGroup>
                    <Label>{translations.number}</Label>
                    <Input className={props.hasErrorFor('number') ? 'form-control is-invalid' : 'form-control'}
                        value={props.recurring_invoice.number}
                        type='text'
                        name='number'
                        id='number'
                        onChange={props.handleInput}
                    />
                    {props.renderErrorFor('number')}
                </FormGroup>

                <FormGroup>
                    <Label for="po_number">{translations.po_number}(*):</Label>
                    <Input value={props.recurring_invoice.po_number} type="text" id="po_number" name="po_number"
                        onChange={props.handleInput}/>
                    {props.renderErrorFor('po_number')}
                </FormGroup>

                <SwitchWithIcon
                    label={translations.auto_billing_enabled}
                    icon={icons.credit_card}
                    checked={props.recurring_invoice.auto_billing_enabled}
                    name="auto_billing_enabled"
                    handleInput={props.handleInput}
                    help_text={translations.auto_billing_enabled_help_text}
                />
            </CardBody>
        </Card>

    )
}
