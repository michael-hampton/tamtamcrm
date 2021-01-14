import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import Datepicker from '../../common/Datepicker'
import { frequencyOptions } from '../../utils/_consts'

export default function Recurring (props) {
    return (
        <Card>
            <CardHeader>{translations.recurring}</CardHeader>
            <CardBody>
                {props.recurring_invoice.last_sent_date.length
                    ? <FormGroup>
                        <Label for="date_to_send">{translations.next_send_date}(*):</Label>
                        <Datepicker name="date_to_send" date={props.recurring_invoice.date_to_send}
                            handleInput={props.handleInput}
                            className={props.hasErrorFor('date_to_send') ? 'form-control is-invalid' : 'form-control'}/>
                        {props.renderErrorFor('date_to_send')}
                    </FormGroup>
                    : <FormGroup>
                        <Label for="start_date">{translations.start_date}(*):</Label>
                        <Datepicker name="start_date" date={props.recurring_invoice.start_date}
                            handleInput={props.handleInput}
                            className={props.hasErrorFor('start_date') ? 'form-control is-invalid' : 'form-control'}/>
                        {props.renderErrorFor('start_date')}
                    </FormGroup>
                }

                <FormGroup>
                    <Label for="expiry_date">{translations.end_date}(*):</Label>
                    <Datepicker name="expiry_date" date={props.recurring_invoice.expiry_date}
                        handleInput={props.handleInput}
                        className={props.hasErrorFor('expiry_date') ? 'form-control is-invalid' : 'form-control'}/>
                    {props.renderErrorFor('expiry_date')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.frequency}</Label>
                    <Input
                        className={props.hasErrorFor('frequency') ? 'form-control is-invalid' : 'form-control'}
                        value={props.recurring_invoice.frequency}
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
                        value={props.recurring_invoice.grace_period}
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
                    {!props.recurring_invoice.is_never_ending &&
                    <React.Fragment>
                        <Label>{translations.number_of_occurances}</Label>
                        <Input
                            value={props.recurring_invoice.number_of_occurances}
                            type='text'
                            name='number_of_occurances'
                            placeholder="Days"
                            id='number_of_occurances'
                            onChange={props.handleInput}
                        />
                    </React.Fragment>

                    }
                    <Label>{translations.never_ending}</Label>
                    <Input type="checkbox" name="is_never_ending" checked={props.recurring_invoice.is_never_ending}
                        onChange={props.handleInput}/>

                </FormGroup>
            </CardBody>
        </Card>

    )
}
