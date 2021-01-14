import React from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import Datepicker from '../../common/Datepicker'
import PaymentTypeDropdown from '../../common/dropdowns/PaymentTypeDropdown'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import { translations } from '../../utils/_translations'

export default class Details extends React.Component {
    constructor (props) {
        super(props)

        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    render () {
        const customer_disabled = this.props.payment.payable_invoices.length > 0
        return (<React.Fragment>
            {!this.props.hide_amount &&
                <FormGroup className="mb-3">
                    <Label>{translations.amount}(*):</Label>
                    <Input value={this.props.payment.amount}
                        className={this.hasErrorFor('amount') ? 'is-invalid' : ''}
                        type="text" name="amount"
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('amount')}
                </FormGroup>
            }

            <FormGroup className="mr-2">
                <Label for="date">{translations.date}(*):</Label>
                <Datepicker name="date" date={this.props.payment.date} handleInput={this.props.handleInput}
                    className={this.hasErrorFor('date') ? 'form-control is-invalid' : 'form-control'}/>
                {this.renderErrorFor('date')}
            </FormGroup>

            <FormGroup className="mb-3">
                <Label>{translations.number}</Label>
                <Input className={this.hasErrorFor('number') ? 'is-invalid' : ''} type="text"
                    value={this.props.payment.number}
                    name="number"
                    onChange={this.props.handleInput}/>
                {this.renderErrorFor('number')}
            </FormGroup>

            <FormGroup className="mb-3">
                <Label>{translations.reference_number}</Label>
                <Input className={this.hasErrorFor('reference_number') ? 'is-invalid' : ''} type="text"
                    value={this.props.payment.reference_number}
                    name="reference_number"
                    onChange={this.props.handleInput}/>
                {this.renderErrorFor('reference_number')}
            </FormGroup>

            <FormGroup className="mb-3">
                <Label>{translations.payment_type}</Label>
                <PaymentTypeDropdown
                    errors={this.props.errors}
                    name="type_id"
                    payment_type={this.props.payment.type_id}
                    renderErrorFor={this.renderErrorFor}
                    handleInputChanges={this.props.handleInput}
                />
                {this.renderErrorFor('type_id')}
            </FormGroup>

            {this.props.hide_customer === false &&
                <FormGroup className="mb-3">
                    <Label>{translations.customer}</Label>
                    <CustomerDropdown
                        disabled={customer_disabled}
                        customer={this.props.payment.customer_id}
                        errors={this.props.errors}
                        name="customer_id"
                        renderErrorFor={this.renderErrorFor}
                        handleInputChanges={this.props.handleInput}
                    />
                    {this.renderErrorFor('customer_id')}
                </FormGroup>
            }
        </React.Fragment>
        )
    }
}
