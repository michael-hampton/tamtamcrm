import React from 'react'
import { Card, CardBody, CardHeader, CustomInput, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import InvoiceDropdown from '../../common/dropdowns/InvoiceDropdown'

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

                <a href="#"
                    className="list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
                    <div className="d-flex w-100 justify-content-between">
                        <h5 className="mb-1">
                            <i style={{ fontSize: '24px', marginRight: '20px' }} className={`fa ${icons.credit_card}`}/>
                            {translations.auto_billing_enabled}
                        </h5>
                        <CustomInput
                            checked={props.recurring_invoice.auto_billing_enabled}
                            type="switch"
                            id="auto_billing_enabled"
                            name="auto_billing_enabled"
                            label=""
                            onChange={props.handleInput}/>
                    </div>

                    <h6 id="passwordHelpBlock" className="form-text text-muted">
                        {translations.auto_billing_enabled_help_text}
                    </h6>
                </a>
            </CardBody>
        </Card>

    )
}
