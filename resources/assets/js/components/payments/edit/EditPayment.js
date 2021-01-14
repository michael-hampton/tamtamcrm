import React from 'react'
import { DropdownItem, Modal, ModalBody } from 'reactstrap'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import InvoiceLine from './InvoiceLine'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import Details from './Details'
import PaymentModel from '../../models/PaymentModel'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import Documents from './Documents'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class EditPayment extends React.Component {
    constructor (props) {
        super(props)

        this.paymentModel = new PaymentModel(this.props.invoices, this.props.payment)
        this.initialState = this.paymentModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.setInvoices = this.setInvoices.bind(this)
        this.setCredits = this.setCredits.bind(this)
        this.setAmount = this.setAmount.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleCustomerChange = this.handleCustomerChange.bind(this)
        this.handleInvoiceChange = this.handleInvoiceChange.bind(this)
        this.handleCreditChange = this.handleCreditChange.bind(this)
        this.handleCheck = this.handleCheck.bind(this)
    }

    handleCheck () {
        this.setState({ send_email: !this.state.checked })
    }

    handleInvoiceChange (e) {
        if (e.target.value === '') {
            return
        }

        const invoice = this.paymentModel.getInvoice(e.target.value)

        if (!invoice) {
            return
        }

        this.setState({
            [e.target.name]: e.target.value,
            customer_id: invoice.customer_id,
            amount: invoice.total
        })

        this.setState({ payable_invoices: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    handleCreditChange (e) {
        if (e.target.value === '') {
            return
        }

        const credit = this.paymentModel.getCredit(e.target.value)

        if (!credit) {
            return
        }

        this.setState({
            [e.target.name]: e.target.value,
            customer_id: credit.customer_id,
            amount: credit.total
        })

        this.setState({ payable_credits: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    handleCustomerChange (customerId) {
        this.setState({ customer_id: customerId }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value,
            changesMade: true
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

    getFormData () {
        return {
            account_id: this.state.account_id,
            type_id: this.state.type_id,
            invoice_id: this.state.invoice_id,
            customer_id: this.state.customer_id,
            amount: this.state.amount,
            reference_number: this.state.reference_number,
            number: this.state.number,
            invoices: this.state.payable_invoices,
            credits: this.state.payable_credits,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            private_notes: this.state.private_notes
        }
    }

    handleClick () {
        this.setState({ loading: true })
        this.paymentModel.update(this.getFormData()).then(response => {
            if (!response) {
                this.setState({ errors: this.paymentModel.errors, message: this.paymentModel.error_message })
                return
            }

            const index = this.props.payments.findIndex(payment => payment.id === this.state.id)
            this.props.payments[index] = response
            this.props.action(this.props.payments)
            this.setState({ changesMade: false, loading: false })
            this.toggle()
        })
    }

    toggle () {
        if (this.state.modal && this.state.changesMade) {
            if (window.confirm('Your changes have not been saved?')) {
                this.setState({ ...this.initialState })
            }

            return
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    setAmount (amount) {
        this.setState({ amount: amount }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
    }

    setInvoices (payableInvoices) {
        this.setState({ payable_invoices: payableInvoices }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
    }

    setCredits (payableCredits) {
        this.setState({ payable_credits: payableCredits }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
    }

    reload (data) {
        this.paymentModel = new PaymentModel(this.props.invoices, data)
        this.initialState = this.paymentModel.fields
        this.initialState.modal = true
        this.setState(this.initialState)
    }

    render () {
        const { message, loading } = this.state

        console.log('invoices a', this.state.payable_invoices)

        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_payment}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_payment}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <DropdownMenuBuilder reload={this.reload.bind(this)} invoices={this.props.payments}
                            formData={this.getFormData()}
                            model={this.paymentModel}
                            action={this.props.action}/>

                        {successMessage}
                        {errorMessage}

                        <Details hide_customer={true} payment={this.state} errors={this.state.errors}
                            hide_amount={this.paymentModel.isCompleted}
                            handleInput={this.handleInput}
                            handleCustomerChange={this.handleCustomerChange} handleCheck={this.handleCheck}/>

                        {!this.paymentModel.isCompleted &&
                        <InvoiceLine payment={this.state} credit_lines={this.state.payable_credits}
                            lines={this.state.payable_invoices} handleAmountChange={this.setAmount}
                            errors={this.state.errors}
                            hideEmpty={false}
                            invoices={this.props.invoices}
                            credits={this.props.credits}
                            customerChange={this.handleCustomerChange}
                            onCreditChange={this.setCredits}
                            onChange={this.setInvoices}/>
                        }

                        <Notes private_notes={this.state.private_notes} handleInput={this.handleInput}/>

                        <Documents payment={this.state}/>

                        <CustomFieldsForm handleInput={this.handleInput} custom_value1={this.state.custom_value1}
                            custom_value2={this.state.custom_value2}
                            custom_value3={this.state.custom_value3}
                            custom_value4={this.state.custom_value4}
                            custom_fields={this.props.custom_fields}/>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={loading}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditPayment
