import React from 'react'
import { DropdownItem, Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import DetailsForm from './DetailsForm'
import SettingsForm from './SettingsForm'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import ExpenseModel from '../../models/ExpenseModel'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import FileUploads from '../../documents/FileUploads'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { getExchangeRateWithMap } from '../../utils/_money'
import FormatMoney from '../../common/FormatMoney'

class EditExpense extends React.Component {
    constructor (props) {
        super(props)

        this.expenseModel = new ExpenseModel(this.props.expense, this.props.customers)
        this.initialState = this.expenseModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    handleInput (e) {
        if (e.target.name === 'currency_id' || e.target.name === 'invoice_currency_id') {
            // const exchange_rate = this.expenseModel.getExchangeRateForCurrency(e.target.value)

            const currencies = JSON.parse(localStorage.getItem('currencies'))
            const exchange_rate = getExchangeRateWithMap(currencies, this.state.currency_id, e.target.value)

            this.setState({ exchange_rate: exchange_rate })
        }

        if (e.target.name === 'amount') {
            this.expenseModel.amount = e.target.value
        }

        if (e.target.name === 'tax_rate' || e.target.name === 'tax_2' || e.target.name === 'tax_3') {
            const name = e.target.options[e.target.selectedIndex].getAttribute('data-name')
            const rate = e.target.options[e.target.selectedIndex].getAttribute('data-rate')
            const tax_rate_name = e.target.name === 'tax' ? 'tax_rate_name' : `tax_rate_name_${e.target.name.split('_')[1]}`

            this.setState({
                [e.target.name]: rate,
                [tax_rate_name]: name,
                changesMade: true
            }, () => {
                this.expenseModel.setTaxValues(this.state)

                const amount_with_tax = this.expenseModel.amountWithTax
                this.setState({ amount_with_tax: amount_with_tax })
            })

            return
        }

        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        const update_tax = e.target.name === 'tax_amount1' || e.target.name === 'tax_amount2' || e.target.name === 'tax_amount3'

        this.setState({
            [e.target.name]: value,
            changesMade: true
        }, () => {
            if (update_tax) {
                this.expenseModel.setTaxValues(this.state)

                const amount_with_tax = this.expenseModel.amountWithTax
                this.setState({ amount_with_tax: amount_with_tax })
            }
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
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

    getFormData () {
        return {
            assigned_to: this.state.assigned_to,
            project_id: this.state.project_id,
            is_recurring: this.state.is_recurring,
            recurring_start_date: this.state.recurring_start_date,
            recurring_end_date: this.state.recurring_end_date,
            recurring_due_date: this.state.recurring_due_date,
            last_sent_date: this.state.last_sent_date,
            next_send_date: this.state.next_send_date,
            recurring_frequency: this.state.recurring_frequency,
            amount: this.state.amount,
            customer_id: this.state.customer_id,
            company_id: this.state.company_id,
            payment_type_id: this.state.payment_type_id,
            expense_category_id: this.state.expense_category_id,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            currency_id: this.state.currency_id,
            exchange_rate: this.state.exchange_rate,
            expense_date: this.state.expense_date,
            payment_date: this.state.payment_date,
            include_documents: this.state.include_documents,
            create_invoice: this.state.create_invoice,
            reference_number: this.state.reference_number,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            tax_rate: this.expenseModel.fields.expense_taxes_calculated_by_amount === true ? this.state.tax_amount1 : this.state.tax_rate,
            tax_2: this.expenseModel.fields.expense_taxes_calculated_by_amount === true ? this.state.tax_amount2 : this.state.tax_2,
            tax_3: this.expenseModel.fields.expense_taxes_calculated_by_amount === true ? this.state.tax_amount3 : this.state.tax_3,
            tax_rate_name: this.state.tax_rate_name,
            tax_rate_name_2: this.state.tax_rate_name_2,
            tax_rate_name_3: this.state.tax_rate_name_3,
            tax_total: this.state.tax_total,
            tax_is_amount: this.expenseModel.fields.expense_taxes_calculated_by_amount === true,
            amount_includes_tax: false
        }
    }

    handleClick () {
        this.setState({ loading: true })
        this.expenseModel.update(this.getFormData()).then(response => {
            if (!response) {
                this.setState({ errors: this.expenseModel.errors, message: this.expenseModel.error_message })
                return
            }

            const index = this.props.expenses.findIndex(expense => expense.id === this.state.id)
            this.props.expenses[index] = response
            this.props.action(this.props.expenses)
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

    reload (data) {
        this.expenseModel = new ExpenseModel(data, this.props.customers)
        this.initialState = this.expenseModel.fields
        this.initialState.modal = true
        this.setState(this.initialState)
    }

    render () {
        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null

        const { message, loading } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_expense}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_expense}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <DropdownMenuBuilder reload={this.reload.bind(this)} invoices={this.props.expenses}
                            formData={this.getFormData()}
                            model={this.expenseModel}
                            action={this.props.action}/>

                        {successMessage}
                        {errorMessage}

                        <Nav tabs>
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '1' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('1')
                                    }}>
                                    {translations.details}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.settings}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.notes}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '4' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('4')
                                    }}>
                                    {translations.documents}
                                </NavLink>
                            </NavItem>
                        </Nav>
                        <TabContent activeTab={this.state.activeTab}>
                            <TabPane tabId="1">
                                <DetailsForm renderErrorFor={this.renderErrorFor} hasErrorFor={this.hasErrorFor}
                                    errors={this.state.errors} model={this.expenseModel}
                                    handleInput={this.handleInput} expense={this.state}
                                    customers={this.props.customers} companies={this.props.companies}/>

                                <CustomFieldsForm handleInput={this.handleInput}
                                    custom_value1={this.state.custom_value1}
                                    custom_value2={this.state.custom_value2}
                                    custom_value3={this.state.custom_value3}
                                    custom_value4={this.state.custom_value4}
                                    custom_fields={this.props.custom_fields}/>

                            </TabPane>

                            <TabPane tabId="2">
                                <SettingsForm errors={this.state.errors} handleInput={this.handleInput}
                                    expense={this.state}/>
                            </TabPane>

                            <TabPane tabId="3">
                                <Notes errors={this.state.errors} public_notes={this.state.public_notes}
                                    private_notes={this.state.private_notes} handleInput={this.handleInput}/>
                            </TabPane>

                            <TabPane tabId="4">
                                <FileUploads entity_type="Expense" entity={this.state}
                                    user_id={this.state.user_id}/>
                            </TabPane>
                        </TabContent>

                        <div className="col-12">
                            <div>
                                {translations.expense_total}: <FormatMoney customers={this.props.customers}
                                    customer_id={this.state.customer_id}
                                    amount={this.expenseModel.grossAmount}/>
                            </div>
                        </div>

                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={loading}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditExpense
