import React, { Component } from 'react'
import { Button, FormGroup, Label, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import InvoiceDropdown from '../common/dropdowns/ExpenseDropdown'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import AddButtons from '../common/AddButtons'
import Notes from '../common/Notes'
import CustomFieldsForm from '../common/CustomFieldsForm'
import { translations } from '../utils/_translations'
import RecurringExpenseModel from '../models/RecurringExpenseModel'
import Details from './Details'

class AddRecurringExpense extends Component {
    constructor (props, context) {
        super(props, context)

        this.recurringExpenseModel = new RecurringExpenseModel(null)
        this.initialState = this.recurringExpenseModel.fields
        this.state = this.initialState
        this.handleInput = this.handleInput.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'recurringExpenseForm')) {
            const storedValues = JSON.parse(localStorage.getItem('recurringExpenseForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
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
            start_date: this.state.start_date,
            expense_id: this.state.expense_id,
            customer_id: this.state.customer_id,
            end_date: this.state.end_date,
            due_date: this.state.due_date,
            frequency: this.state.frequency,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes
        }

        this.recurringExpenseModel.save(data).then(response => {
            if (!response) {
                this.setState({
                    errors: this.recurringExpenseModel.errors,
                    message: this.recurringExpenseModel.error_message
                })
                return
            }
            this.props.expenses.push(response)
            this.props.action(this.props.expenses)
            this.setState(this.initialState)
            localStorage.removeItem('recurringExpenseForm')
        })
    }

    handleInput (e) {
        let customerId = this.state.customer_id

        if (e.target.name === 'expense_id') {
            const expense = this.props.allExpenses.filter(function (expense) {
                return expense.id === parseInt(e.target.value)
            })

            if (!expense.length) {
                return
            }

            customerId = expense[0].customer_id
        }

        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            customer_id: customerId,
            [e.target.name]: value
        }, () => localStorage.setItem('recurringExpenseForm', JSON.stringify(this.state)))

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
                this.setState(this.initialState, () => localStorage.removeItem('recurringExpenseForm'))
            }
        })
    }

    render () {
        const inlineClass = this.props ? 'mb-4' : 'form-inline mb-4'

        const form = (
            <div className={inlineClass}>
                <Details recurring_expense={this.state} hasErrorFor={this.hasErrorFor}
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
                    <ModalHeader toggle={this.toggle}>
                        {translations.add_recurring_expense}
                    </ModalHeader>

                    <ModalBody>
                        {form}
                        <FormGroup>
                            <Label>{translations.expense}</Label>
                            <InvoiceDropdown
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
                    <ModalFooter>
                        <Button color="primary" onClick={this.handleClick.bind(this)}>{translations.save}</Button>
                        <Button color="secondary" onClick={this.toggle}>{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
            : form
    }
}

export default AddRecurringExpense
