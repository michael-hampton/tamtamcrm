import React, {Component} from 'react'
import {Card, CardBody, Nav, NavItem, NavLink, TabContent, TabPane} from 'reactstrap'
import axios from 'axios'
import CustomFieldSettingsForm from './CustomFieldSettingsForm'
import {translations} from '../utils/_translations'
import {consts} from '../utils/_consts'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import EditScaffold from "./EditScaffold";

class CustomFieldSettings extends Component {
    constructor(props) {
        super(props)

        this.modules = JSON.parse(localStorage.getItem('modules'))

        this.state = {
            success: false,
            error: false,
            activeTab: 0,
            quotes: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            users: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            companies: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            customers: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            product: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            invoices: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            payments: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            tasks: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            credits: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            expenses: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}],
            orders: [{name: 'custom_value1', label: '', type: consts.text}, {
                name: 'custom_value2',
                label: '',
                type: consts.text
            }, {
                name: 'custom_value3',
                label: '',
                type: consts.text
            }, {name: 'custom_value4', label: '', type: consts.text}]
        }

        this.handleChange = this.handleChange.bind(this)
        this.toggle = this.toggle.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getSettings = this.getSettings.bind(this)
        this.handleOptionChange = this.handleOptionChange.bind(this)
    }

    componentDidMount() {
        this.getSettings()
    }

    getSettings() {
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
                this.setState({error: true})
            })
    }

    handleChange(e) {
        const entity = e.target.dataset.entity
        const id = e.target.dataset.id
        const className = e.target.dataset.field
        const value = e.target.value

        if (['type', 'label'].includes(className)) {
            const products = [...this.state[entity]]
            products[id][className] = value
            this.setState({[entity]: products}, () => console.log(this.state))
        } else {
            // this.setState({ [e.target.name]: e.target.value })
        }

        if (className === 'type' && value === 'select' && !this.state[entity].options) {
            const products = [...this.state[entity]]
            products[id].options = [{value: '', text: ''}]
            this.setState({[entity]: products}, () => console.log(this.state))
        }
    }

    handleOptionChange(e) {
        console.log('entity', e)
        const entity = e.data_entity
        const id = e.data_id

        const products = [...this.state[entity]]
        products[id].options = e.options
        this.setState({[entity]: products}, () => console.log(this.state))
        console.log('element', e)
    }

    handleSubmit(e) {
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
            this.setState({success: true})
            localStorage.setItem('custom_fields', JSON.stringify(fields))
        })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.setState({
                        errors: error.response.data.errors
                    })
                } else {
                    this.setState({message: error.response.data})
                }
            })
    }

    toggle(event, tab) {
        if (this.state.activeTab !== tab) {
            this.setState({activeTab: tab})
        }
    }

    handleClose() {
        this.setState({success: false, error: false})
    }

    handleCancel() {
        this.setState({settings: this.state.cached_settings, changesMade: false})
    }

    render() {
        const {
            users,
            customers,
            product,
            invoices,
            payments,
            companies,
            quotes,
            credits,
            tasks,
            expenses,
            orders
        } = this.state
        let tabCounter = 1

        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggle
            },
            tabs: [],
            children: []
        }

        const tabContent = []
        const tabItems = []

        if (customers && this.modules && this.modules.customers === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.customers})
            tabCounter++
        }

        if (product && this.modules && this.modules.products === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.products})

            tabCounter++
        }

        if (invoices && this.modules && this.modules.invoices === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.invoices})

            tabCounter++
        }

        if (payments && this.modules && this.modules.payments === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.payments})

            tabCounter++
        }

        if (companies && this.modules && this.modules.companies === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.companies})

            tabCounter++
        }

        if (quotes && this.modules && this.modules.quotes === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.quotes})

            tabCounter++
        }

        if (credits && this.modules && this.modules.credits === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.credits})

            tabCounter++
        }

        if (tasks && this.modules && this.modules.tasks === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.tasks})

            tabCounter++
        }

        if (expenses && this.modules && this.modules.expenses === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.expenses})

            tabCounter++
        }

        if (orders && this.modules && this.modules.orders === true) {
            tabs.children.push(<Card>
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
            </Card>)

            tabs.tabs.push({label: translations.orders})

            tabCounter++
        }

        tabs.children.push(<Card>
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
        </Card>)

        tabs.tabs.push({label: translations.users})

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                                 message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                                 message={this.state.settings_not_saved}/>

                <EditScaffold title={translations.custom_fields} cancelButtonDisabled={!this.state.changesMade}
                              handleCancel={this.handleCancel.bind(this)}
                              handleSubmit={this.handleSubmit.bind(this)}
                              tabs={tabs}/>
            </React.Fragment>
        )
    }
}

export default CustomFieldSettings
