import React, { Component } from 'react'
import axios from 'axios'
import {
    Button,
    Col,
    DropdownItem,
    Modal,
    ModalBody,
    Nav,
    NavItem,
    NavLink,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import 'react-dates/lib/css/_datepicker.css'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import AddButtons from '../../common/AddButtons'
import Details from './Details'
import Contacts from './Contacts'
import Items from './Items'
import Documents from './Documents'
import Notes from '../../common/Notes'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import InvoiceSettings from '../../common/InvoiceSettings'
import { CalculateLineTotals, CalculateSurcharges, CalculateTotal } from '../../common/InvoiceCalculations'
import CreditModel from '../../models/CreditModel'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import Emails from '../../emails/Emails'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import { consts } from '../../utils/_consts'
import NoteTabs from '../../common/NoteTabs'
import Detailsm from './Detailsm'
import Contactsm from './Contactsm'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import CustomerModel from '../../models/CustomerModel'
import TotalsBox from '../../invoice/edit/TotalsBox'
import InvoiceReducer from '../../invoice/InvoiceReducer'
import TaskRepository from '../../repositories/TaskRepository'
import ExpenseRepository from '../../repositories/ExpenseRepository'
import ProjectRepository from '../../repositories/ProjectRepository'
import { getExchangeRateWithMap } from '../../utils/_money'

export default class EditCredit extends Component {
    constructor (props, context) {
        super(props, context)

        const data = this.props.credit ? this.props.credit : null
        this.creditModel = new CreditModel(data, this.props.customers)
        this.initialState = this.creditModel.fields
        this.state = this.initialState

        this.updateData = this.updateData.bind(this)
        this.saveData = this.saveData.bind(this)
        this.setTotal = this.setTotal.bind(this)
        this.toggle = this.toggle.bind(this)
        this.handleTaskChange = this.handleTaskChange.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.buildForm = this.buildForm.bind(this)
        this.setRecurring = this.setRecurring.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleAddFiled = this.handleAddFiled.bind(this)
        this.handleFieldChange = this.handleFieldChange.bind(this)
        this.updatePriceData = this.updatePriceData.bind(this)
        this.calculateTotals = this.calculateTotals.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.handleContactChange = this.handleContactChange.bind(this)
        this.handleSurcharge = this.handleSurcharge.bind(this)
        this.calculateSurcharges = this.calculateSurcharges.bind(this)
        this.loadEntity = this.loadEntity.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)

        this.total = 0
        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentDidMount () {
        if (!this.state.id) {
            if (Object.prototype.hasOwnProperty.call(localStorage, 'creditForm')) {
                const storedValues = JSON.parse(localStorage.getItem('creditForm'))
                this.setState({ ...storedValues }, () => console.log('new state', this.state))
            }
        }

        if (this.props.credit && this.props.credit.customer_id) {
            const contacts = this.creditModel.contacts
            this.setState({ contacts: contacts })
        }

        if (this.props.entity_id && this.props.entity_type) {
            this.loadEntity(this.props.entity_type)
        }
    }

    // make sure to remove the listener
    // when the component is not mounted anymore
    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
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

            this.creditModel.customer_id = data.customer_id
            const contacts = this.creditModel.contacts

            this.setState({
                changesMade: true,
                contacts: contacts,
                modalOpen: true,
                line_type: line_type,
                line_items: data.line_items,
                customer_id: data.customer_id
            }, () => {
                console.log(`creating new invoice for ${this.props.entity_type} ${this.props.entity_id}`)
            })

            return response
        })
    }

    setRecurring (recurring) {
        this.setState({ recurring: recurring, changesMade: true })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab, changesMade: true })
        }
    }

    handleWindowSizeChange () {
        this.setState({ is_mobile: window.innerWidth <= 768 })
    }

    handleInput (e) {
        const original_customer_id = this.state.customer_id
        if (e.target.name === 'customer_id') {
            const customer_data = this.creditModel.customerChange(e.target.value)

            this.setState({
                changesMade: true,
                customerName: customer_data.name,
                contacts: customer_data.contacts,
                address: customer_data.address
            }, () => {
                this.creditModel.customer_id = e.target.value
                localStorage.setItem('creditForm', JSON.stringify(this.state))
            })

            if (this.settings.convert_product_currency === true) {
                const customer = new CustomerModel(customer_data.customer)
                const currency_id = customer.currencyId

                const currencies = JSON.parse(localStorage.getItem('currencies'))
                const exchange_rate = getExchangeRateWithMap(currencies, this.state.currency_id, currency_id)
                this.setState({ changesMade: true, exchange_rate: exchange_rate, currency_id: currency_id })

                // const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === currency_id)
                // const exchange_rate = currency[0].exchange_rate
            }

            if (this.state.project_id && original_customer_id !== parseInt(e.target.value)) {
                this.setState({ changesMade: true, project_id: '' })
            }
        }

        if (e.target.name === 'tax' || e.target.name === 'tax_2' || e.target.name === 'tax_3') {
            const name = e.target.options[e.target.selectedIndex].getAttribute('data-name')
            const rate = e.target.options[e.target.selectedIndex].getAttribute('data-rate')
            const tax_rate_name = e.target.name === 'tax' ? 'tax_rate_name' : `tax_rate_name_${e.target.name.split('_')[1]}`

            this.setState({
                [e.target.name]: rate,
                [tax_rate_name]: name,
                changesMade: true
            }, () => {
                localStorage.setItem('invoiceForm', JSON.stringify(this.state))
                this.calculateTotals()
            })

            return
        }

        if (e.target.name === 'partial') {
            const has_partial = e.target.value.trim() !== ''
            this.setState({ changesMade: true, has_partial: has_partial, partial: e.target.value })
            return
        }

        if (e.target.name === 'is_amount_discount') {
            this.setState({ changesMade: true, is_amount_discount: e.target.value === 'true' })
            return
        }

        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value

        this.setState({
            changesMade: true,
            [e.target.name]: value
        }, () => localStorage.setItem('creditForm', JSON.stringify(this.state)))
    }

    handleSurcharge (e) {
        const value = (!e.target.value) ? ('') : ((e.target.type === 'checkbox') ? (e.target.checked) : (parseFloat(e.target.value)))

        this.setState({
            changesMade: true,
            [e.target.name]: value
        }, () => this.calculateSurcharges())
    }

    calculateSurcharges (x) {
        const surcharge_totals = CalculateSurcharges({ surcharges: this.state })

        this.setState({
            changesMade: true,
            total_custom_values: surcharge_totals.total_custom_values,
            total_custom_tax: surcharge_totals.total_custom_tax
        }, () => this.calculateTotals())
    }

    handleTaskChange (e) {
        axios.get(`/api/products/tasks/${this.props.task_id}/1,2`)
            .then((r) => {
                const arrLines = []
                let total = 0

                if (r.data && r.data.line_items) {
                    r.data.line_items.map((product) => {
                        const objLine = {
                            quantity: product.quantity,
                            product_id: product.product_id,
                            unit_price: product.unit_price,
                            unit_discount: product.unit_discount,
                            unit_tax: product.unit_tax,
                            order_id: r.data.id
                        }

                        total += parseFloat(product.unit_price)
                        arrLines.push(objLine)
                    })
                }

                this.setState({
                    changesMade: true,
                    line_items: arrLines,
                    total: total
                }, () => localStorage.setItem('creditForm', JSON.stringify(this.state)))
            })
            .catch((e) => {
                console.warn(e)
            })
    }

    toggle () {
        if (this.state.modalOpen && this.state.changesMade) {
            if (!window.confirm('Your changes have not been saved?')) {
                return false
            }
        }

        this.setState({
            modalOpen: !this.state.modalOpen,
            errors: []
        }, () => {
            if (!this.state.modalOpen && !this.state.id) {
                this.setState({
                    changesMade: false,
                    public_notes: '',
                    tax: null,
                    tax_rate_name: '',
                    tax_rate_name_2: '',
                    tax_rate_name_3: '',
                    tax_2: null,
                    tax_3: null,
                    private_notes: '',
                    transaction_fee: null,
                    shipping_cost: null,
                    gateway_fee: null,
                    gateway_percentage: false,
                    transaction_fee_tax: null,
                    shipping_cost_tax: null,
                    custom_value1: '',
                    custom_value2: '',
                    custom_value3: '',
                    custom_value4: '',
                    terms: '',
                    footer: '',
                    partial: 0,
                    partial_due_date: null,
                    invoice_id: null,
                    customer_id: null,
                    company_id: null,
                    status_id: null,
                    line_items: [],
                    invitations: [],
                    return_to_stock: false
                }, () => localStorage.removeItem('creditForm'))
            }
        })
    }

    updateData (rowData) {
        this.setState(prevState => ({
            line_items: [...prevState.line_items, rowData]
        }), () => localStorage.setItem('creditForm', JSON.stringify(this.state)))
    }

    calculateTotals () {
        const totals = CalculateTotal({ invoice: this.state })

        this.setState({
            changesMade: true,
            total: totals.total,
            discount_total: totals.discount_total,
            tax_total: totals.tax_total,
            sub_total: totals.sub_total
        }, () => localStorage.setItem('creditForm', JSON.stringify(this.state)))
    }

    updatePriceData (index) {
        const line_items = this.state.line_items.slice()
        line_items[index] = CalculateLineTotals({
            currentRow: line_items[index],
            settings: this.settings,
            invoice: this.state
        })

        this.setState({
            changesMade: true,
            line_items: line_items
        }, () => localStorage.setItem('creditForm', JSON.stringify(this.state)))
    }

    handleFieldChange (line_items, row) {
        this.setState({ changesMade: true, line_items: line_items }, () => {
            this.calculateTotals()
            this.updatePriceData(row)
        })
    }

    handleAddFiled (type_id = 1) {
        this.setState((prevState, props) => {
            return {
                line_items: this.state.line_items.concat({
                    is_amount_discount: false,
                    unit_discount: 0,
                    unit_tax: 0,
                    quantity: 0,
                    unit_price: 0,
                    product_id: 0,
                    type_id: type_id
                })
            }
        })
    }

    handleDelete (idx) {
        const newTasks = this.state.line_items.filter((task, tIndex) => {
            return idx !== tIndex
        })

        this.setState({ changesMade: true, line_items: newTasks })
    }

    setTotal (total) {
        this.total = total
    }

    getFormData () {
        return {
            currency_id: this.state.currency_id,
            exchange_rate: this.state.exchange_rate,
            return_to_stock: this.state.return_to_stock,
            is_amount_discount: this.state.is_amount_discount,
            assigned_to: this.state.assigned_to,
            number: this.state.number,
            account_id: this.state.account_id,
            design_id: this.state.design_id,
            tax_rate: this.state.tax,
            tax_rate_name: this.state.tax_rate_name,
            tax_rate_name_2: this.state.tax_rate_name_2,
            tax_2: this.state.tax_2,
            tax_rate_name_3: this.state.tax_rate_name_3,
            tax_3: this.state.tax_3,
            task_id: this.props.task_id,
            due_date: this.state.due_date,
            customer_id: this.state.customer_id,
            company_id: this.state.company_id,
            line_items: this.state.line_items,
            total: this.state.total,
            balance: this.props.credit && this.props.credit.balance ? this.props.credit.balance : this.state.total,
            sub_total: this.state.sub_total,
            tax_total: this.state.tax_total,
            discount_total: this.state.discount_total,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            po_number: this.state.po_number,
            terms: this.state.terms,
            footer: this.state.footer,
            date: this.state.date,
            partial: this.state.partial,
            partial_due_date: this.state.partial_due_date,
            recurring: this.state.recurring,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            transaction_fee: this.state.transaction_fee,
            transaction_fee_tax: this.state.transaction_fee_tax,
            shipping_cost: this.state.shipping_cost,
            shipping_cost_tax: this.state.shipping_cost_tax,
            invitations: this.state.invitations,
            gateway_fee: this.state.gateway_fee,
            gateway_percentage: this.state.gateway_percentage
        }
    }

    saveData () {
        this.setState({ loading: true })

        this.creditModel.save(this.getFormData()).then(response => {
            if (!response) {
                this.setState({
                    errors: this.creditModel.errors,
                    message: this.creditModel.error_message,
                    showErrorMessage: true,
                    loading: false
                })
                return
            }

            if (!this.state.id) {
                const firstInvoice = response
                const allInvoices = this.props.credits
                allInvoices.push(firstInvoice)
                this.props.action(allInvoices)
                localStorage.removeItem('creditForm')
                this.setState(this.initialState)
                return
            }

            const index = this.props.credits.findIndex(credit => credit.id === this.state.id)
            this.props.credits[index] = response
            this.props.action(this.props.credits)
            this.setState({ loading: false, changesMade: false })
        })
    }

    handleContactChange (e) {
        const invitations = this.creditModel.buildInvitations(e.target.value, e.target.checked)
        // update the state with the new array of options
        this.setState({ invitations: invitations }, () => console.log('invitations', invitations))
    }

    reload (data) {
        this.creditModel = new CreditModel(data, this.props.customers)
        this.initialState = this.creditModel.fields
        this.initialState.modalOpen = true
        this.setState(this.initialState)
    }

    buildForm () {
        const successMessage = this.state.showSuccessMessage !== false && this.state.showSuccessMessage !== ''
            ? <SuccessMessage message={this.state.showSuccessMessage}/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message={this.state.message.length > 0 ? this.state.message : 'Something went wrong'}/> : null

        const tabs = <Nav tabs className="nav-justified disable-scrollbars">
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
                    {translations.contacts}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '3' ? 'active' : ''}
                    onClick={() => {
                        this.toggleTab('3')
                    }}>
                    {translations.items}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '4' ? 'active' : ''}
                    onClick={() => {
                        this.toggleTab('4')
                    }}>
                    {translations.notes}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '5' ? 'active' : ''}
                    onClick={() => {
                        this.toggleTab('5')
                    }}>
                    {translations.documents}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '6' ? 'active' : ''}
                    onClick={() => {
                        this.toggleTab('6')
                    }}>
                    {translations.email}
                </NavLink>
            </NavItem>
        </Nav>

        const details = this.state.is_mobile
            ? <Detailsm hide_customer={this.state.id === null} address={this.state.address}
                customerName={this.state.customerName} handleInput={this.handleInput}
                customers={this.props.customers}
                errors={this.state.errors} credit={this.state}/>
            : <Details address={this.state.address} customerName={this.state.customerName} handleInput={this.handleInput}
                customers={this.props.customers}
                errors={this.state.errors} credit={this.state}/>

        const custom = <CustomFieldsForm handleInput={this.handleInput} custom_value1={this.state.custom_value1}
            custom_value2={this.state.custom_value2}
            custom_value3={this.state.custom_value3}
            custom_value4={this.state.custom_value4}
            custom_fields={this.props.custom_fields}/>

        const contacts = this.state.is_mobile
            ? <Contactsm address={this.state.address} customerName={this.state.customerName}
                handleInput={this.handleInput} invoice={this.state}
                errors={this.state.errors}
                contacts={this.state.contacts}
                invitations={this.state.invitations}
                handleContactChange={this.handleContactChange}/>
            : <Contacts address={this.state.address} hide_customer={this.state.id === null}
                customerName={this.state.customerName}
                handleInput={this.handleInput} invoice={this.state} errors={this.state.errors}
                contacts={this.state.contacts}
                invitations={this.state.invitations} handleContactChange={this.handleContactChange}/>

        const settings = <InvoiceSettings is_mobile={this.state.is_mobile} handleSurcharge={this.handleSurcharge}
            settings={this.state}
            errors={this.state.errors} handleInput={this.handleInput}
            discount={this.state.discount}
            is_amount_discount={this.state.is_amount_discount}
            design_id={this.state.design_id}/>

        const items = <Items line_type={this.state.line_type} model={this.creditModel} customers={this.props.customers}
            credit={this.state} errors={this.state.errors}
            handleFieldChange={this.handleFieldChange}
            handleAddFiled={this.handleAddFiled} setTotal={this.setTotal}
            handleDelete={this.handleDelete}/>

        const notes = !this.state.is_mobile
            ? <NoteTabs show_exchange={this.creditModel.account_currency.exchange_rate !== this.state.exchange_rate}
                invoice={this.state} private_notes={this.state.private_notes}
                public_notes={this.state.public_notes}
                terms={this.state.terms} footer={this.state.footer} errors={this.state.errors}
                handleInput={this.handleInput}/>
            : <Notes private_notes={this.state.private_notes} public_notes={this.state.public_notes}
                terms={this.state.terms} footer={this.state.footer} errors={this.state.errors}
                handleInput={this.handleInput}/>

        const email_editor = this.state.id
            ? <Emails model={this.creditModel} emails={this.state.emails} template="email_template_credit"
                show_editor={true}
                customers={this.props.customers} entity_object={this.state} entity="credit"
                entity_id={this.state.id}/> : null

        const documents = this.state.id ? <Documents credit={this.state}/> : null

        const dropdownMenu = this.state.id
            ? <DropdownMenuBuilder reload={this.reload.bind(this)} invoices={this.props.credits}
                formData={this.getFormData()}
                model={this.creditModel}
                task_id={this.state.task_id}
                handleTaskChange={this.handleTaskChange}
                action={this.props.action}/> : null

        const form = this.state.is_mobile
            ? <React.Fragment>

                {tabs}

                <TabContent activeTab={this.state.activeTab} className="bg-transparent">
                    <TabPane tabId="1">
                        {details}
                        {custom}
                    </TabPane>

                    <TabPane tabId="2">
                        {contacts}
                    </TabPane>

                    <TabPane tabId="3">
                        {settings}
                        {items}
                    </TabPane>

                    <TabPane tabId="4">
                        {notes}
                    </TabPane>

                    <TabPane tabId="5">
                        {documents}
                    </TabPane>

                    <TabPane tabId="6">
                        {email_editor}
                    </TabPane>
                </TabContent>
            </React.Fragment>

            : <React.Fragment>
                <Nav tabs>
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('1')
                            }}>
                            {translations.credit}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}>
                            {translations.email}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}>
                            {translations.documents}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab} className="bg-transparent">
                    <TabPane tabId="1">
                        <Row form>
                            <Col md={4}>
                                {details}
                                {custom}
                            </Col>

                            <Col md={4}>
                                {settings}
                            </Col>

                            <Col md={4}>
                                {contacts}
                            </Col>
                        </Row>
                        {items}

                        <Row form>
                            <Col md={8}>
                                {notes}
                            </Col>

                            <Col md={3} className="m-3">
                                <TotalsBox invoice={this.state}/>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="2">
                        {email_editor}
                    </TabPane>

                    <TabPane tabId="3">
                        {documents}
                    </TabPane>
                </TabContent>

            </React.Fragment>

        return (
            <div>
                {dropdownMenu}
                {successMessage}
                {errorMessage}
                {form}

            </div>
        )
    }

    render () {
        const form = this.buildForm()
        const { success, loading } = this.state
        const button = this.props.add === true ? <AddButtons toggle={this.toggle}/>
            : <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>Edit</DropdownItem>
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        if (this.props.modal) {
            return (
                <React.Fragment>
                    {button}
                    <Modal isOpen={this.state.modalOpen} toggle={this.toggle} className={this.props.className}
                        size="lg">
                        <DefaultModalHeader toggle={this.toggle}
                            title={this.creditModel.isNew ? translations.add_credit : translations.edit_credit}/>

                        <ModalBody className={theme}>
                            {form}
                        </ModalBody>
                        <DefaultModalFooter show_success={true} toggle={this.toggle} saveData={this.saveData}
                            loading={loading}/>
                    </Modal>
                </React.Fragment>
            )
        }

        return (
            <div>

                {success && <div className="alert alert-success" role="alert">
                    Products added to task successfully
                </div>}

                {form}
                <Button color="success" onClick={this.saveData}>Save</Button>
            </div>
        )
    }
}
