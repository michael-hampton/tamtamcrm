import React, { Component } from 'react'
import { FormGroup, Label, Modal, ModalBody } from 'reactstrap'
import InvoiceDropdown from '../../common/dropdowns/InvoiceDropdown'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import AddButtons from '../../common/AddButtons'
import Notes from '../../common/Notes'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import { translations } from '../../utils/_translations'
import RecurringInvoiceModel from '../../models/RecurringInvoiceModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Recurring from './Recurring'
import TaskRepository from '../../repositories/TaskRepository'
import ExpenseRepository from '../../repositories/ExpenseRepository'
import ProjectRepository from '../../repositories/ProjectRepository'
import { consts } from '../../utils/_consts'
import InvoiceReducer from '../../invoice/InvoiceReducer'

class AddRecurringInvoice extends Component {
    constructor (props, context) {
        super(props, context)

        this.recurringInvoiceModel = new RecurringInvoiceModel(null)
        this.initialState = this.recurringInvoiceModel.fields
        this.state = this.initialState
        this.handleInput = this.handleInput.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.toggle = this.toggle.bind(this)
        this.loadEntity = this.loadEntity.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'recurringInvoiceForm')) {
            const storedValues = JSON.parse(localStorage.getItem('recurringInvoiceForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }

        if (this.props.entity_id && this.props.entity_type) {
            this.loadEntity(this.props.entity_type)
        }
    }

    loadEntity (type) {
        const repo = (type === 'task') ? (new TaskRepository()) : ((type === 'expense') ? (new ExpenseRepository()) : (new ProjectRepository()))
        const line_type = (type === 'task') ? (consts.line_item_task) : ((type === 'expense') ? (consts.line_item_expense) : (consts.line_item_project))
        const reducer = new InvoiceReducer(this.props.entity_id, this.props.entity_type)
        repo.getById(this.props.entity_id).then(response => {
            if (!response) {
                alert('error')
            }

            console.log('task', response)

            const data = reducer.build(type, response)

            this.recurringInvoiceModel.customer_id = data.customer_id
            // const contacts = this.recurringInvoiceModel.contacts

            this.setState({
                // contacts: contacts,
                modal: true,
                // line_type: line_type,
                // line_items: data.line_items,
                customer_id: data.customer_id
            }, () => {
                console.log(`creating new invoice for ${this.props.entity_type} ${this.props.entity_id}`)
            })

            return response
        })
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        const data = {
            account_id: this.state.account_id,
            start_date: this.state.start_date,
            invoice_id: this.state.invoice_id,
            customer_id: this.state.customer_id,
            expiry_date: this.state.expiry_date,
            due_date: this.state.due_date,
            frequency: this.state.frequency,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            grace_period: this.state.grace_period,
            auto_billing_enabled: this.state.auto_billing_enabled,
            number_of_occurrances: this.state.number_of_occurrances
        }

        this.recurringInvoiceModel.save(data).then(response => {
            if (!response) {
                this.setState({
                    errors: this.recurringInvoiceModel.errors,
                    message: this.recurringInvoiceModel.error_message
                })
                return
            }
            this.props.invoices.push(response)
            this.props.action(this.props.invoices)
            this.setState(this.initialState)
            localStorage.removeItem('recurringInvoiceForm')
        })
    }

    handleInput (e) {
        let customerId = this.state.customer_id

        if (e.target.name === 'invoice_id') {
            const invoice = this.props.allInvoices.filter(function (invoice) {
                return invoice.id === parseInt(e.target.value)
            })

            if (!invoice.length) {
                return
            }

            customerId = invoice[0].customer_id
        }

        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            customer_id: customerId,
            [e.target.name]: value
        }, () => localStorage.setItem('recurringInvoiceForm', JSON.stringify(this.state)))

        if (this.props.setRecurring) {
            this.props.setRecurring(JSON.stringify(this.state))
        }
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('recurringInvoiceForm'))
            }
        })
    }

    render () {
        const inlineClass = this.props ? 'mb-4' : 'form-inline mb-4'
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        const form = (
            <div className={inlineClass}>
                <Recurring recurring_invoice={this.state} hasErrorFor={this.hasErrorFor}
                    renderErrorFor={this.renderErrorFor} handleInput={this.handleInput}/>

                <CustomFieldsForm handleInput={this.handleInput} custom_fields={this.props.custom_fields}
                    custom_value1={this.state.custom_value1} custom_value2={this.state.custom_value2}
                    custom_value3={this.state.custom_value3} custom_value4={this.state.custom_value4}/>
            </div>
        )

        return this.props.modal === true
            ? <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_recurring_invoice}/>

                    <ModalBody className={theme}>
                        {form}
                        <FormGroup>
                            <Label>{translations.invoice}</Label>
                            <InvoiceDropdown
                                is_recurring={true}
                                invoices={this.props.allInvoices}
                                handleInputChanges={this.handleInput}
                                name="invoice_id"
                                errors={this.state.errors}
                            />
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.customer}</Label>
                            <CustomerDropdown
                                disabled={true}
                                handleInputChanges={this.handleInput}
                                customer={this.state.customer_id}
                                customers={this.props.customers}
                                errors={this.state.errors}
                            />
                        </FormGroup>

                        <Notes private_notes={this.state.private_notes} public_notes={this.state.public_notes}
                            handleInput={this.handleInput}/>
                    </ModalBody>
                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
            : form
    }
}

export default AddRecurringInvoice
