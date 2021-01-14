import React, { Component } from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import FormBuilder from '../../settings/FormBuilder'
import Datepicker from '../../common/Datepicker'
import { translations } from '../../utils/_translations'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import CompanyDropdown from '../../common/dropdowns/CompanyDropdown'
import ProjectDropdown from '../../common/dropdowns/ProjectDropdown'

export default class Detailsm extends Component {
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
        const customFields = this.props.custom_fields ? this.props.custom_fields : []

        if (customFields[0] && Object.keys(customFields[0]).length) {
            customFields[0].forEach((element, index, array) => {
                if (this.props[element.name] && this.props[element.name].length) {
                    customFields[0][index].value = this.props[element.name]
                }
            })
        }

        const customForm = customFields && customFields.length ? <FormBuilder
            handleChange={this.props.handleInput.bind(this)}
            formFieldsRows={customFields}
        /> : null

        return (
            <Card>
                <CardHeader>{translations.details}</CardHeader>
                <CardBody>
                    <FormGroup>
                        <Label for="date">{translations.date}(*):</Label>
                        <Datepicker name="date" date={this.props.purchase_order.date}
                            handleInput={this.props.handleInput}
                            className={this.hasErrorFor('date') ? 'form-control is-invalid' : 'form-control'}/>
                        {this.renderErrorFor('due_date')}
                    </FormGroup>
                    <FormGroup>
                        <Label for="due_date">{translations.needed_by}(*):</Label>
                        <Datepicker name="due_date" date={this.props.purchase_order.due_date}
                            handleInput={this.props.handleInput}
                            className={this.hasErrorFor('due_date') ? 'form-control is-invalid' : 'form-control'}/>
                        {this.renderErrorFor('due_date')}
                    </FormGroup>
                    <FormGroup>
                        <Label for="po_number">{translations.po_number}(*):</Label>
                        <Input value={this.props.purchase_order.po_number} type="text" id="po_number" name="po_number"
                            onChange={this.props.handleInput}/>
                        {this.renderErrorFor('po_number')}
                    </FormGroup>
                    <FormGroup>
                        <Label>{translations.partial}</Label>
                        <Input
                            value={this.props.purchase_order.partial}
                            type='text'
                            name='partial'
                            id='partial'
                            onChange={this.props.handleInput}
                        />
                    </FormGroup>

                    <FormGroup className={this.props.purchase_order.has_partial === true ? '' : 'd-none'}>
                        <Label>{translations.partial_due_date}</Label>
                        <Datepicker name="partial_due_date" date={this.props.purchase_order.partial_due_date}
                            handleInput={this.props.handleInput}
                            className={this.hasErrorFor('partial_due_date') ? 'form-control is-invalid' : 'form-control'}/>
                    </FormGroup>

                    <FormGroup>
                        <Label for="postcode">{translations.assigned_user}:</Label>
                        <UserDropdown
                            user_id={this.props.purchase_order.assigned_to}
                            name="assigned_to"
                            errors={this.props.errors}
                            handleInputChanges={this.props.handleInput}
                        />
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.project}</Label>
                        <ProjectDropdown
                            projects={this.props.projects}
                            renderErrorFor={this.renderErrorFor}
                            name="project_id"
                            handleInputChanges={this.props.handleInput}
                            project={this.props.purchase_order.project_id}
                            customer_id={this.props.purchase_order.customer_id}
                        />
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.number}</Label>
                        <Input className={this.hasErrorFor('number') ? 'form-control is-invalid' : 'form-control'}
                            value={this.props.purchase_order.number}
                            type='text'
                            name='number'
                            id='number'
                            onChange={this.props.handleInput}
                        />
                        {this.renderErrorFor('number')}
                    </FormGroup>

                    {this.props.hide_customer === true &&
                    <FormGroup>
                        <Label>{translations.company}</Label>
                        <CompanyDropdown
                            handleInputChanges={this.props.handleInput}
                            company_id={this.props.purchase_order.company_id}
                            companies={this.props.companies}
                            errors={this.props.errors}
                        />
                    </FormGroup>
                    }

                    {customForm}
                </CardBody>
            </Card>

        )
    }
}
