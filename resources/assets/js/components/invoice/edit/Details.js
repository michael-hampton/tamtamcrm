import React, { Component } from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import Datepicker from '../../common/Datepicker'
import { translations } from '../../utils/_translations'

export default class Details extends Component {
    constructor (props, context) {
        super(props, context)
        this.state = {
            is_recurring: false
        }
        this.handleSlideClick = this.handleSlideClick.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    handleSlideClick (e) {
        this.setState({ is_recurring: e.target.checked })
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
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
        return (
            <Card>
                <CardHeader>{translations.details}</CardHeader>
                <CardBody>
                    <FormGroup>
                        <Label for="date">{translations.date}(*):</Label>
                        <Datepicker name="date" date={this.props.invoice.date} handleInput={this.props.handleInput}
                            className={this.hasErrorFor('date') ? 'form-control is-invalid' : 'form-control'}/>
                        {this.renderErrorFor('date')}
                    </FormGroup>
                    <FormGroup>
                        <Label for="due_date">{translations.due_date}(*):</Label>
                        <Datepicker name="due_date" date={this.props.invoice.due_date}
                            handleInput={this.props.handleInput}
                            className={this.hasErrorFor('due_date') ? 'form-control is-invalid' : 'form-control'}/>
                        {this.renderErrorFor('due_date')}
                    </FormGroup>
                    <FormGroup>
                        <Label for="po_number">{translations.po_number}(*):</Label>
                        <Input value={this.props.invoice.po_number} type="text" id="po_number" name="po_number"
                            onChange={this.props.handleInput}/>
                        {this.renderErrorFor('po_number')}
                    </FormGroup>
                    <FormGroup>
                        <Label>{translations.partial}</Label>
                        <Input
                            value={this.props.invoice.partial}
                            type='text'
                            name='partial'
                            id='partial'
                            onChange={this.props.handleInput}
                        />
                    </FormGroup>

                    <FormGroup className={this.props.invoice.has_partial === true ? '' : 'd-none'}>
                        <Label>{translations.partial_due_date}</Label>
                        <Datepicker name="partial_due_date" date={this.props.invoice.partial_due_date}
                            handleInput={this.props.handleInput}
                            className={this.hasErrorFor('partial_due_date') ? 'form-control is-invalid' : 'form-control'}/>
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.number}</Label>
                        <Input className={this.hasErrorFor('number') ? 'form-control is-invalid' : 'form-control'}
                            value={this.props.invoice.number}
                            type='text'
                            name='number'
                            id='number'
                            onChange={this.props.handleInput}
                        />
                        {this.renderErrorFor('number')}
                    </FormGroup>
                </CardBody>
            </Card>

        )
    }
}
