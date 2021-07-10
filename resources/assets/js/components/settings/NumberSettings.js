import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from '../common/EditScaffold'

class NumberSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            loaded: false,
            activeTab: 0,
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            success: false,
            error: false,
            changesMade: false,
            isSaving: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.toggle = this.toggle.bind(this)

        this.model = new CompanyModel({ id: this.state.id })
    }

    componentDidMount () {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getAccount()
    }

    componentWillUnmount () {
        window.removeEventListener('beforeunload', this.beforeunload.bind(this))
    }

    beforeunload (e) {
        if (this.state.changesMade) {
            if (!confirm(translations.changes_made_warning)) {
                e.preventDefault()
                return false
            }
        }
    }

    toggle (event, tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings,
                cached_settings: response.settings
            }, () => {
                console.log(response)
            })
        })
    }

    handleChange (event) {
        this.setState({ [event.target.name]: event.target.value })
    }

    handleSettingsChange (event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        this.setState(prevState => ({
            changesMade: true,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleSubmit (e) {
        this.setState({ isSaving: true })
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('_method', 'PUT')

        axios.post(`/api/accounts/${this.state.id}`, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({
                    success: true,
                    cached_settings: this.state.settings,
                    changesMade: false,
                    isSaving: false
                }, () => this.model.updateSettings(this.state.settings))
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    getSettingFields () {
        const settings = this.state.settings

        console.log('settings', settings)

        return [
            [
                {
                    name: 'recurring_number_prefix',
                    label: 'Recurring Prefix',
                    type: 'text',
                    placeholder: 'Recurring Prefix',
                    value: settings.recurring_number_prefix,
                    group: 1
                },
                {
                    name: 'counter_padding',
                    label: 'Counter Padding',
                    type: 'text',
                    placeholder: 'Counter Padding',
                    value: settings.counter_padding
                }
            ]
        ]
    }

    getInvoiceFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'invoice_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.invoice_number_prefix,
                    group: 1
                },
                {
                    name: 'invoice_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.invoice_number_counter
                },
                {
                    name: 'invoice_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.invoice_counter_type || ''
                }
            ]
        ]
    }

    getProjectFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'project_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.project_number_prefix,
                    group: 1
                },
                {
                    name: 'project_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.project_number_counter
                },
                {
                    name: 'project_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.project_counter_type || ''
                }
            ]
        ]
    }

    getExpenseFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'expense_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.expense_number_prefix,
                    group: 1
                },
                {
                    name: 'expense_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.expense_number_counter
                },
                {
                    name: 'expense_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.expense_counter_type || ''
                }
            ]
        ]
    }

    getCompanyFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'company_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.company_number_prefix,
                    group: 1
                },
                {
                    name: 'company_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.company_number_counter
                },
                {
                    name: 'company_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.company_counter_type || ''
                }
            ]
        ]
    }

    getPurchaseOrderFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'purchaseorder_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.purchaseorder_number_prefix,
                    group: 1
                },
                {
                    name: 'purchaseorder_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.purchaseorder_number_counter
                },
                {
                    name: 'purchaseorder_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.purchaseorder_counter_type
                }
            ]
        ]
    }

    getDealFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'deal_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.deal_number_prefix,
                    group: 1
                },
                {
                    name: 'deal_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.deal_number_counter
                },
                {
                    name: 'deal_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.deal_counter_type
                }
            ]
        ]
    }

    getCaseFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'case_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.case_number_prefix,
                    group: 1
                },
                {
                    name: 'case_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.case_number_counter
                },
                {
                    name: 'case_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.case_counter_type
                }
            ]
        ]

        return formFields
    }

    getTaskFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'task_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.task_number_prefix,
                    group: 1
                },
                {
                    name: 'task_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.task_number_counter
                },
                {
                    name: 'task_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.task_counter_type
                }
            ]
        ]
    }

    getRecurringInvoiceFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'recurringinvoice_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.recurringinvoice_number_prefix,
                    group: 1
                },
                {
                    name: 'recurringinvoice_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.recurringinvoice_number_counter
                },
                {
                    name: 'recurringinvoice_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.recurringinvoice_counter_type
                }
            ]
        ]
    }

    getRecurringQuoteFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'recurringquote_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.recurringquote_number_prefix,
                    group: 1
                },
                {
                    name: 'recurringquote_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.recurringquote_number_counter
                },
                {
                    name: 'recurringquote_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.recurringquote_counter_type
                }
            ]
        ]
    }

    getOrderFields () {
        const settings = this.state.settings

        console.log('settings', settings)

        const formFields = [
            [
                {
                    name: 'order_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.order_number_prefix,
                    group: 1
                },
                {
                    name: 'order_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.order_number_counter
                },
                {
                    name: 'order_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.order_counter_type
                }
            ]
        ]

        return formFields
    }

    getQuoteFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'quote_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.quote_number_prefix,
                    group: 1
                },
                {
                    name: 'quote_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.quote_number_counter
                },
                {
                    name: 'quote_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.quote_counter_type
                },
                {
                    name: 'quote_design_id',
                    label: 'Quote Design',
                    type: 'select',
                    value: settings.quote_design_id,
                    options: [
                        {
                            value: '1',
                            text: 'Clean'
                        },
                        {
                            value: '2',
                            text: 'Bold'
                        },
                        {
                            value: '3',
                            text: 'Modern'
                        },
                        {
                            value: '4',
                            text: 'Plain'
                        }
                    ],
                    group: 1
                }
            ]
        ]
    }

    getCreditFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'credit_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.credit_number_prefix,
                    group: 1
                },
                {
                    name: 'credit_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.credit_number_counter
                },
                {
                    name: 'credit_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.credit_counter_type
                }
                // {
                //     name: 'credit_design_id',
                //     label: 'Credit Design',
                //     type: 'select',
                //     value: settings.credit_design_id,
                //     options: [
                //         {
                //             value: '1',
                //             text: 'Clean'
                //         },
                //         {
                //             value: '2',
                //             text: 'Bold'
                //         },
                //         {
                //             value: '3',
                //             text: 'Modern'
                //         },
                //         {
                //             value: '4',
                //             text: 'Plain'
                //         }
                //     ],
                //     group: 1
                // }
            ]
        ]
    }

    getPaymentFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'payment_number_prefix',
                    label: translations.number_prefix,
                    type: 'text',
                    placeholder: translations.number_prefix,
                    value: settings.payment_number_prefix
                },
                {
                    name: 'payment_counter_type',
                    label: translations.counter_type,
                    type: 'select',
                    options: [
                        {
                            value: 'customer',
                            text: translations.customer
                        },
                        {
                            value: 'group',
                            text: translations.group
                        }
                    ],
                    placeholder: translations.counter_type,
                    value: settings.payment_counter_type
                },
                {
                    name: 'payment_terms',
                    label: 'Payment Terms',
                    type: 'select',
                    placeholder: 'Payment Terms',
                    value: settings.payment_terms,
                    options: [
                        {
                            value: '1',
                            text: 'Yes'
                        },
                        {
                            value: '0',
                            text: 'No'
                        }
                    ]
                }
            ]
        ]
    }

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const modules = JSON.parse(localStorage.getItem('modules'))

        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggle
            },
            tabs: [
                {
                    label: translations.settings
                }
            ],
            children: []
        }

        tabs.children.push(<Card>
            <CardBody>
                <FormBuilder
                    handleChange={this.handleSettingsChange}
                    formFieldsRows={this.getSettingFields()}
                />
            </CardBody>
        </Card>)

        if (modules && modules.invoices) {
            tabs.tabs.push({ label: translations.invoices })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getInvoiceFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.quotes) {
            tabs.tabs.push({ label: translations.quotes })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getQuoteFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.payments) {
            tabs.tabs.push({ label: translations.payments })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPaymentFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.credits) {
            tabs.tabs.push({ label: translations.credits })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getCreditFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.orders) {
            tabs.tabs.push({ label: translations.orders })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getOrderFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.purchase_orders) {
            tabs.tabs.push({ label: translations.purchase_orders })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPurchaseOrderFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.deals) {
            tabs.tabs.push({ label: translations.deals })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getDealFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.cases) {
            tabs.tabs.push({ label: translations.cases })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getCaseFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.tasks) {
            tabs.tabs.push({ label: translations.tasks })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getTaskFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.recurringInvoices) {
            tabs.tabs.push({ label: translations.recurring_invoices_abbr })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getRecurringInvoiceFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.recurringQuotes) {
            tabs.tabs.push({ label: translations.recurring_quotes_abbr })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getRecurringQuoteFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.expenses) {
            tabs.tabs.push({ label: translations.expenses })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getExpenseFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.projects) {
            tabs.tabs.push({ label: translations.projects })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getProjectFields()}
                    />
                </CardBody>
            </Card>)
        }

        if (modules && modules.companies) {
            tabs.tabs.push({ label: translations.companies })
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getCompanyFields()}
                    />
                </CardBody>
            </Card>)
        }

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <EditScaffold isAdvancedSettings={true} isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                    isEditing={this.state.changesMade}
                    title={translations.number_settings}
                    cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit.bind(this)}
                    tabs={tabs}/>
            </React.Fragment>
        ) : null
    }
}

export default NumberSettings
