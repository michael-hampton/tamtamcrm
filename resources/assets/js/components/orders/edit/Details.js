import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import Datepicker from '../../common/Datepicker'
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
        return (<Card>
            <CardHeader>{translations.details}</CardHeader>
            <CardBody>
                <FormGroup className="mr-2">
                    <Label for="date">{translations.date}(*):</Label>
                    <Datepicker name="date" date={this.props.order.date} handleInput={this.props.handleInput}
                        className={this.hasErrorFor('date') ? 'form-control is-invalid' : 'form-control'}/>
                    {this.renderErrorFor('date')}
                </FormGroup>

                <FormGroup>
                    <Label for="due_date">{translations.due_date}(*):</Label>
                    <Datepicker name="due_date" date={this.props.order.due_date}
                        handleInput={this.props.handleInput}
                        className={this.hasErrorFor('due_date') ? 'form-control is-invalid' : 'form-control'}/>
                    {this.renderErrorFor('due_date')}
                </FormGroup>

                <FormGroup>
                    <Label for="po_number">{translations.po_number}(*):</Label>
                    <Input value={this.props.order.po_number} type="text" id="po_number" name="po_number"
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('po_number')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.number}</Label>
                    <Input className={this.hasErrorFor('number') ? 'form-control is-invalid' : 'form-control'}
                        value={this.props.order.number}
                        type='text'
                        name='number'
                        id='number'
                        onChange={this.props.handleInput}
                    />
                    {this.renderErrorFor('number')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.customer}</Label>
                    <CustomerDropdown
                        handleInputChanges={this.props.handleInput}
                        customer={this.props.order.customer_id}
                        customers={this.props.customers}
                        errors={this.props.errors}
                    />
                </FormGroup>
            </CardBody>
        </Card>
        )
    }
}
