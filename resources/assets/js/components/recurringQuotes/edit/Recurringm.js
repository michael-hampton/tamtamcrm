import React from 'react'
import { Card, CardBody, CardHeader, CustomInput, FormGroup, Input, Label } from 'reactstrap'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import Datepicker from '../../common/Datepicker'
import { translations } from '../../utils/_translations'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import QuoteDropdown from '../../common/dropdowns/QuoteDropdown'
import { frequencyOptions } from '../../utils/_consts'
import { icons } from '../../utils/_icons'

export default function Recurringm (props) {
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

                {props.recurring_quote.last_sent_date.length
                    ? <FormGroup>
                        <Label for="date_to_send">{translations.next_send_date}(*):</Label>
                        <Datepicker name="date_to_send" date={props.recurring_quote.date_to_send}
                            handleInput={props.handleInput}
                            className={props.hasErrorFor('date_to_send') ? 'form-control is-invalid' : 'form-control'}/>
                        {props.renderErrorFor('date_to_send')}
                    </FormGroup>
                    : <FormGroup>
                        <Label for="start_date">{translations.start_date}(*):</Label>
                        <Datepicker name="start_date" date={props.recurring_quote.start_date}
                            handleInput={props.handleInput}
                            className={props.hasErrorFor('start_date') ? 'form-control is-invalid' : 'form-control'}/>
                        {props.renderErrorFor('start_date')}
                    </FormGroup>
                }

                <FormGroup>
                    <Label for="expiry_date">{translations.end_date}(*):</Label>
                    <Datepicker name="expiry_date" date={props.recurring_quote.expiry_date}
                        handleInput={props.handleInput}
                        className={props.hasErrorFor('expiry_date') ? 'form-control is-invalid' : 'form-control'}/>
                    {props.renderErrorFor('expiry_date')}
                </FormGroup>

                <FormGroup>
                    <Label for="po_number">{translations.po_number}(*):</Label>
                    <Input value={props.recurring_quote.po_number} type="text" id="po_number" name="po_number"
                        onChange={props.handleInput}/>
                    {props.renderErrorFor('po_number')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.frequency}</Label>
                    <Input
                        className={props.hasErrorFor('frequency') ? 'form-control is-invalid' : 'form-control'}
                        value={props.recurring_quote.frequency}
                        type='select'
                        name='frequency'
                        placeholder="Days"
                        id='frequency'
                        onChange={props.handleInput}
                    >
                        <option value="">{translations.select_frequency}</option>
                        {Object.keys(frequencyOptions).map((frequency) => (
                            <option value={frequency}>{translations[frequencyOptions[frequency]]}</option>
                        ))}
                    </Input>
                    {props.renderErrorFor('frequency')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.grace_period}</Label>
                    <Input
                        value={props.recurring_quote.grace_period}
                        type='text'
                        name='grace_period'
                        placeholder="Days"
                        id='grace_period'
                        onChange={props.handleInput}
                    />

                    <h6 id="passwordHelpBlock" className="form-text text-muted">
                        {translations.grace_period_help_text}
                    </h6>
                </FormGroup>

                <FormGroup>
                    <Label>{translations.number_of_occurances}</Label>
                    <Input
                        value={props.recurring_quote.number_of_occurances}
                        type='text'
                        name='number_of_occurances'
                        placeholder="Days"
                        id='number_of_occurances'
                        onChange={props.handleInput}
                    />
                </FormGroup>

                <a href="#"
                    className="list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
                    <div className="d-flex w-100 justify-content-between">
                        <h5 className="mb-1">
                            <i style={{ fontSize: '24px', marginRight: '20px' }} className={`fa ${icons.credit_card}`}/>
                            {translations.auto_billing_enabled}
                        </h5>
                        <CustomInput
                            checked={props.recurring_quote.auto_billing_enabled}
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

                <FormGroup>
                    <Label for="postcode">{translations.assigned_user}:</Label>
                    <UserDropdown
                        user_id={props.recurring_quote.assigned_to}
                        name="assigned_to"
                        errors={props.errors}
                        handleInputChanges={props.handleInput}
                    />
                </FormGroup>

                {props.hide_customer === true &&
                <FormGroup>
                    <Label>{translations.customer}</Label>
                    <CustomerDropdown
                        handleInputChanges={props.handleInput}
                        customer={props.recurring_quote.customer_id}
                        customers={props.customers}
                        errors={props.errors}
                    />
                </FormGroup>
                }
            </CardBody>
        </Card>
    )
}
