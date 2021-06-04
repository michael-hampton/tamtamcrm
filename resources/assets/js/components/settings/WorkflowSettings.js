import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'
import { consts } from '../utils/_consts'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from '../common/EditScaffold'

export default class WorkflowSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            activeTab: 0,
            success: false,
            error: false,
            changesMade: false,
            isSaving: false,
            loaded: false
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

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
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

    getPurchaseOrderFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_email_purchase_order',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_purchase_order,
                    group: 1
                },
                {
                    name: 'should_archive_purchase_order',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_purchase_order,
                    group: 1
                }
            ]
        ]
    }

    getInvoiceFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_lock_invoice',
                    label: translations.lock_invoice,
                    type: 'select',
                    value: settings.should_lock_invoice,
                    options: [
                        {
                            value: consts.lock_invoices_off,
                            text: translations.off
                        },
                        {
                            value: consts.lock_invoices_sent,
                            text: translations.when_sent
                        },
                        {
                            value: consts.lock_invoices_paid,
                            text: translations.when_paid
                        }
                    ]
                },
                {
                    name: 'should_email_invoice',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_invoice,
                    group: 1
                },
                {
                    name: 'should_archive_invoice',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_invoice,
                    group: 1
                }
            ]
        ]
    }

    getOrderFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_email_order',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_order,
                    group: 1
                },
                {
                    name: 'should_archive_order',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_order,
                    group: 1
                },
                {
                    name: 'should_convert_order',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_order,
                    group: 1
                },
                {
                    name: 'order_charge_point',
                    label: translations.order_charge_point,
                    type: 'select',
                    value: settings.order_charge_point,
                    options: [
                        {
                            value: consts.order_charge_point_create,
                            text: translations.when_created
                        },
                        {
                            value: consts.order_charge_point_sent,
                            text: translations.when_sent
                        }
                    ]
                }
            ]
        ]
    }

    getLeadFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_email_lead',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_lead,
                    group: 1
                },
                {
                    name: 'should_archive_lead',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_lead,
                    group: 1
                },
                {
                    name: 'should_convert_lead',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_lead,
                    group: 1
                }
            ]
        ]
    }

    getQuoteFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_email_quote',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_quote,
                    group: 1
                },
                {
                    name: 'should_archive_quote',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_quote,
                    group: 1
                },
                {
                    name: 'should_convert_quote',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_quote,
                    group: 1
                }
            ]
        ]
    }

    getDealFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_email_deal',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_deal,
                    group: 1
                },
                {
                    name: 'should_archive_deal',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_deal,
                    group: 1
                },
                {
                    name: 'should_convert_deal',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_deal,
                    group: 1
                }
            ]
        ]
    }

    getPaymentFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'invoice_payment_deleted_status',
                    label: translations.invoice_payment_deleted_status,
                    icon: `fa ${icons.envelope}`,
                    type: 'select',
                    options: [
                        {
                            value: consts.invoice_status_draft,
                            text: translations.draft
                        },
                        {
                            value: consts.invoice_status_sent,
                            text: translations.sent
                        },
                        {
                            value: 100,
                            text: translations.deleted
                        }
                    ],
                    value: settings.invoice_payment_deleted_status,
                    group: 1
                },
                {
                    name: 'credit_payment_deleted_status',
                    label: translations.credit_payment_deleted_status,
                    icon: `fa ${icons.envelope}`,
                    type: 'select',
                    options: [
                        {
                            value: consts.credit_status_draft,
                            text: translations.draft
                        },
                        {
                            value: consts.credit_status_sent,
                            text: translations.sent
                        },
                        {
                            value: 100,
                            text: translations.deleted
                        }
                    ],
                    value: settings.credit_payment_deleted_status,
                    group: 1
                }
            ]
        ]
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
            tabs: [],
            children: []
        }

        if (modules && modules.invoices) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getInvoiceFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.invoices })
        }

        if (modules && modules.quotes) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getQuoteFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.quotes })
        }

        if (modules && modules.leads) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getLeadFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.leads })
        }

        if (modules && modules.orders) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getOrderFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.orders })
        }

        if (modules && modules.deals) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getOrderFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.deals })
        }

        if (modules && modules.purchase_orders) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPurchaseOrderFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.POS })
        }

        if (modules && modules.payments) {
            tabs.children.push(<Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPaymentFields()}
                    />
                </CardBody>
            </Card>)
            tabs.tabs.push({ label: translations.payments })
        }

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <EditScaffold isAdvancedSettings={true} isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                    isEditing={this.state.changesMade} fullWidth={true}
                    title={translations.workflow_settings}
                    cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit.bind(this)}
                    tabs={tabs}/>

            </React.Fragment>
        ) : null
    }
}
