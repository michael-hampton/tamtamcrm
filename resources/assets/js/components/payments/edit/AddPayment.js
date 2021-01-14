import React from 'react'
import { Button, Card, CardBody, CustomInput, Modal, ModalBody } from 'reactstrap'
import InvoiceLine from './InvoiceLine'
import AddButtons from '../../common/AddButtons'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import Details from './Details'
import PaymentModel from '../../models/PaymentModel'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { icons } from '../../utils/_icons'
import { toast, ToastContainer } from 'react-toastify'

class AddPayment extends React.Component {
    constructor (props) {
        super(props)

        this.paymentModel = new PaymentModel(this.props.invoices)
        this.initialState = this.paymentModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleCustomerChange = this.handleCustomerChange.bind(this)
        this.setInvoices = this.setInvoices.bind(this)
        this.setCredits = this.setCredits.bind(this)
        this.handleCheck = this.handleCheck.bind(this)
        this.setAmount = this.setAmount.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'paymentForm')) {
            const storedValues = JSON.parse(localStorage.getItem('paymentForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value
        })
    }

    handleCheck () {
        this.setState({ send_email: !this.state.send_email }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
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

    handleCustomerChange (customerId) {
        this.setState({ customer_id: customerId }, () => localStorage.setItem('paymentForm', JSON.stringify(this.state)))
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
        const credit_sum = this.state.payable_credits.reduce(function (a, b) {
            return a + parseFloat(b.amount)
        }, 0)

        const invoice_sum = this.state.payable_invoices.reduce(function (a, b) {
            return a + parseFloat(b.amount)
        }, 0)

        const total = invoice_sum - credit_sum

        if (total < 0) {
            toast.error(translations.negative_payment_error, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            return false
        }

        this.setState({ loading: true })
        const data = {
            account_id: this.state.account_id,
            date: this.state.date,
            type_id: this.state.type_id,
            invoices: this.state.payable_invoices,
            credits: this.state.payable_credits,
            customer_id: this.state.customer_id,
            amount: this.state.amount,
            send_email: this.state.send_email,
            number: this.state.number,
            reference_number: this.state.reference_number,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            private_notes: this.state.private_notes
        }

        this.paymentModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.paymentModel.errors, message: this.paymentModel.error_message }, () => {
                    if (this.paymentModel.error_message && this.paymentModel.error_message.length) {
                        toast.error(this.paymentModel.error_message, {
                            position: 'top-center',
                            autoClose: 5000,
                            hideProgressBar: false,
                            closeOnClick: true,
                            pauseOnHover: true,
                            draggable: true,
                            progress: undefined
                        })
                    }
                })
                return
            }

            if (this.props.payments) {
                this.props.payments.push(response)
                this.props.action(this.props.payments)
            } else {
                // TODO
            }

            localStorage.removeItem('paymentForm')
            this.setState(this.initialState)
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('paymentForm'))
            }
        })
    }

    render () {
        const { loading } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const form = <React.Fragment>
            <Card>
                <CardBody>
                    <Details hide_customer={false} payment={this.state} errors={this.state.errors}
                        handleInput={this.handleInput}
                        handleCustomerChange={this.handleCustomerChange} handleCheck={this.handleCheck}/>

                    <InvoiceLine invoice_id={this.props.invoice_id} payment={this.state} status={2}
                        invoiceStatus={'2,4'}
                        creditStatus={'2,3'}
                        hideEmpty={true}
                        handleAmountChange={this.setAmount}
                        errors={this.state.errors}
                        invoices={this.props.invoices} credits={this.props.credits}
                        customerChange={this.handleCustomerChange}
                        onChange={this.setInvoices}
                        onCreditChange={this.setCredits}/>

                    <a href="#"
                        className="list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">
                                <i style={{ fontSize: '24px', marginRight: '20px' }}
                                    className={`fa ${icons.credit_card}`}/>
                                {translations.send_email}
                            </h5>
                            <CustomInput
                                checked={this.state.send_email}
                                type="switch"
                                id="send_email"
                                name="send_email"
                                label=""
                                onChange={this.handleCheck}/>
                        </div>

                        <h6 id="passwordHelpBlock" className="form-text text-muted">
                            {translations.email_receipt}
                        </h6>
                    </a>

                    {!!this.props.custom_fields &&
                    <CustomFieldsForm handleInput={this.handleInput} custom_value1={this.state.custom_value1}
                        custom_value2={this.state.custom_value2}
                        custom_value3={this.state.custom_value3}
                        custom_value4={this.state.custom_value4}
                        custom_fields={this.props.custom_fields}/>
                    }

                </CardBody>
            </Card>

            <Notes private_notes={this.state.private_notes} handleInput={this.handleInput}/>

        </React.Fragment>

        const modal = <React.Fragment>
            <AddButtons toggle={this.toggle}/>
            <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                <DefaultModalHeader toggle={this.toggle} title={translations.add_payment}/>

                <ModalBody className={theme}>

                    <ToastContainer
                        position="top-center"
                        autoClose={5000}
                        hideProgressBar={false}
                        newestOnTop={false}
                        closeOnClick
                        rtl={false}
                        pauseOnFocusLoss
                        draggable
                        pauseOnHover
                    />

                    {form}
                </ModalBody>

                <DefaultModalFooter show_success={true} toggle={this.toggle}
                    saveData={this.handleClick.bind(this)}
                    loading={loading}/>
            </Modal>
        </React.Fragment>

        return !this.props.showForm ? modal
            : <React.Fragment>
                {form}
                <Button color="success" onClick={this.handleClick.bind(this)}>{translations.save}</Button>
            </React.Fragment>
    }
}

export default AddPayment
