import React, { Component } from 'react'
import { Card, CardBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import CustomFieldSettingsForm from './CustomFieldSettingsForm'
import { translations } from '../utils/_translations'
import { consts } from '../utils/_consts'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'

class CustomFieldSettings extends Component {
    constructor (props) {
        super(props)

        this.modules = JSON.parse(localStorage.getItem('modules'))

        this.state = {
            success: false,
            error: false,
            activeTab: '1',
            quotes: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            users: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            companies: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            customers: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            product: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            invoices: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            payments: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            tasks: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            credits: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            expenses: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }],
            orders: [{ name: 'custom_value1', label: '', type: consts.text }, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, { name: 'custom_value4', label: '', type: consts.text }]
        }

        this.handleChange = this.handleChange.bind(this)
        this.toggle = this.toggle.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getSettings = this.getSettings.bind(this)
        this.handleOptionChange = this.handleOptionChange.bind(this)
    }

    componentDidMount () {
        this.getSettings()
    }

    getSettings () {
        axios.get('api/accounts/fields/getAllCustomFields')
            .then((r) => {
                if (r.data.Customer && Object.keys(r.data)) {
                    this.setState({
                        // orders: r.data.Order,
                        expenses: r.data.Expense,
                        product: r.data.Product,
                        customers: r.data.Customer,
                        payments: r.data.Payment,
                        invoices: r.data.Invoice,
                        companies: r.data.Company,
                        quotes: r.data.Quote,
                        credits: r.data.Credit,
                        tasks: r.data.Task
                    })
                    console.log('response', r.data.Product)
                }
            })
            .catch((e) => {
                this.setState({ error: true })
            })
    }

    handleChange (e) {
        const entity = e.target.dataset.entity
        const id = e.target.dataset.id
        const className = e.target.dataset.field
        const value = e.target.value

        if (['type', 'label'].includes(className)) {
            const products = [...this.state[entity]]
            products[id][className] = value
            this.setState({ [entity]: products }, () => console.log(this.state))
        } else {
            // this.setState({ [e.target.name]: e.target.value })
        }

        if (className === 'type' && value === 'select' && !this.state[entity].options) {
            const products = [...this.state[entity]]
            products[id].options = [{ value: '', text: '' }]
            this.setState({ [entity]: products }, () => console.log(this.state))
        }
    }

    handleOptionChange (e) {
        console.log('entity', e)
        const entity = e.data_entity
        const id = e.data_id

        const products = [...this.state[entity]]
        products[id].options = e.options
        this.setState({ [entity]: products }, () => console.log(this.state))
        console.log('element', e)
    }

    handleSubmit (e) {
        const fields = {}
        fields.Order = this.state.orders
        fields.Product = this.state.product
        fields.Customer = this.state.customers
        fields.Company = this.state.companies
        fields.Payment = this.state.payments
        fields.Invoice = this.state.invoices
        fields.Quote = this.state.quotes
        fields.Task = this.state.tasks
        fields.Credit = this.state.credits
        fields.Expense = this.state.expenses

        axios.post('/api/accounts/fields', {
            fields: JSON.stringify(fields)
        }).then((response) => {
            this.setState({ success: true })
            localStorage.setItem('custom_fields', JSON.stringify(fields))
        })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.setState({
                        errors: error.response.data.errors
                    })
                } else {
                    this.setState({ message: error.response.data })
                }
            })
    }

    toggle (e) {
        const tab = String(e.target.dataset.id)
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }

        const parent = e.currentTarget.parentNode
        const rect = parent.getBoundingClientRect()
        const rect2 = parent.nextSibling.getBoundingClientRect()
        const rect3 = parent.previousSibling.getBoundingClientRect()
        const winWidth = window.innerWidth || document.documentElement.clientWidth
        const widthScroll = winWidth * 33 / 100

        if (rect.left <= 10 || rect3.left <= 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft -= widthScroll
        }

        if (rect.right >= winWidth - 10 || rect2.right >= winWidth - 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft += widthScroll
        }
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const { users, customers, product, invoices, payments, companies, quotes, credits, tasks, expenses, orders } = this.state
        let tabCounter = 1
        const tabContent = []
        const tabItems = []

        if (customers && this.modules && this.modules.customers === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            customers.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={customers[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="customers" type={customers[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={customers[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.customers}
                </NavLink>
            </NavItem>)
            tabCounter++
        }

        if (product && this.modules && this.modules.products === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            product.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={product[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="product" type={product[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={product[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.products}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (invoices && this.modules && this.modules.invoices === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            invoices.map((val, idx) => {
                                const catId = `custom_name${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={invoices[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="invoices" type={invoices[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={invoices[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.invoices}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (payments && this.modules && this.modules.payments === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            payments.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={payments[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="payments" type={payments[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={payments[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.payments}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (companies && this.modules && this.modules.companies === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            companies.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={companies[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="companies" type={companies[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={companies[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.companies}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (quotes && this.modules && this.modules.quotes === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            quotes.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={quotes[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="quotes" type={quotes[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={quotes[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.quotes}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (credits && this.modules && this.modules.credits === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            credits.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={credits[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="credits" type={credits[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={credits[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    data-id={tabCounter}
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    onClick={this.toggle}>
                    {translations.credits}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (tasks && this.modules && this.modules.tasks === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            tasks.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={tasks[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="tasks" type={tasks[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={tasks[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    data-id={tabCounter}
                    onClick={this.toggle}>
                    {translations.tasks}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (expenses && this.modules && this.modules.expenses === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            expenses.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={expenses[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="expenses" type={expenses[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={expenses[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    data-id={tabCounter}
                    onClick={this.toggle}>
                    {translations.expenses}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        if (orders && this.modules && this.modules.orders === true) {
            tabContent.push(<TabPane tabId={String(tabCounter)}>
                <Card>
                    <CardBody>
                        {
                            orders.map((val, idx) => {
                                const catId = `custom_value${idx}`
                                const ageId = `age-${idx}`
                                return <CustomFieldSettingsForm idx={idx} age={ageId} obj={orders[idx]}
                                    handleOptionChange={this.handleOptionChange}
                                    entity="orders" type={orders[idx].type}
                                    handleChange={this.handleChange} catId={catId}
                                    label={orders[idx].label}/>
                            })
                        }
                    </CardBody>
                </Card>
            </TabPane>)

            tabItems.push(<NavItem>
                <NavLink
                    className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                    data-id={tabCounter}
                    onClick={this.toggle}>
                    {translations.orders}
                </NavLink>
            </NavItem>)

            tabCounter++
        }

        tabContent.push(<TabPane tabId={String(tabCounter)}>
            <Card>
                <CardBody>
                    {
                        users.map((val, idx) => {
                            const catId = `custom_value${idx}`
                            const ageId = `age-${idx}`
                            return <CustomFieldSettingsForm idx={idx} age={ageId} obj={users[idx]}
                                handleOptionChange={this.handleOptionChange}
                                entity="users" type={users[idx].type}
                                handleChange={this.handleChange} catId={catId}
                                label={users[idx].label}/>
                        })
                    }
                </CardBody>
            </Card>
        </TabPane>)

        tabItems.push(<NavItem>
            <NavLink
                className={this.state.activeTab === String(tabCounter) ? 'active' : ''}
                data-id={tabCounter}
                onClick={this.toggle}>
                {translations.users}
            </NavLink>
        </NavItem>)

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={this.state.settings_not_saved}/>

                <Header title={translations.custom_fields} handleSubmit={this.handleSubmit}
                    tabs={<Nav tabs className="setting-tabs disable-scrollbars">
                        {tabItems}
                    </Nav>}/>

                <div className="settings-container settings-container-narrow fixed-margin-mobile">
                    <TabContent activeTab={this.state.activeTab}>
                        {tabContent}
                    </TabContent>
                </div>
            </React.Fragment>
        )
    }
}

export default CustomFieldSettings
