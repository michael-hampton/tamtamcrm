import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import BlockButton from '../common/BlockButton'

export default class ExpenseSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            settings: {},
            activeTab: '1',
            success: false,
            error: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    componentDidMount () {
        this.getAccount()
    }

    toggle (tab, e) {
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

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings
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
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleSubmit (e) {
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('_method', 'PUT')

        axios.post(`/api/accounts/${this.state.id}`, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({ success: true })
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    getExpenseFields () {
        const settings = this.state.settings

        const fields = [
            {
                name: 'expense_approval_required',
                label: translations.expense_approval_required,
                icon: `fa ${icons.envelope}`,
                type: 'switch',
                value: settings.expense_approval_required,
                help_text: translations.expense_approval_required_help,
                group: 1
            },
            {
                name: 'expense_auto_create_invoice',
                label: translations.expense_auto_create_invoice,
                icon: `fa ${icons.envelope}`,
                type: 'switch',
                value: settings.expense_auto_create_invoice,
                help_text: translations.expense_auto_create_invoice_help,
                group: 1
            },
            {
                name: 'create_expense_invoice',
                label: translations.create_expense_invoice,
                icon: `fa ${icons.envelope}`,
                type: 'switch',
                value: settings.create_expense_invoice,
                help_text: translations.create_expense_invoice_help,
                group: 1
            },
            {
                name: 'include_expense_documents',
                label: translations.include_expense_documents,
                icon: `fa ${icons.archive}`,
                type: 'switch',
                value: settings.include_expense_documents,
                help_text: translations.include_expense_documents_help,
                group: 1
            },
            {
                name: 'create_expense_payment',
                label: translations.create_expense_payment,
                icon: `fa ${icons.archive}`,
                type: 'switch',
                value: settings.create_expense_payment,
                help_text: translations.create_expense_payment_help,
                group: 1
            },
            {
                name: 'convert_expense_currency',
                label: translations.convert_expense_currency,
                icon: `fa ${icons.archive}`,
                type: 'switch',
                value: settings.convert_expense_currency,
                help_text: translations.convert_expense_currency_help,
                group: 1
            }
        ]

        if (settings.show_tax_rate1 === true || settings.show_tax_rate2 === true || settings.show_tax_rate3 === true) {
            fields.push(
                {
                    name: 'expense_taxes_calculated_by_amount',
                    label: translations.enter_taxes,
                    type: 'select',
                    options: [
                        {
                            value: 'true',
                            text: translations.by_amount
                        },
                        {
                            value: 'false',
                            text: translations.by_rate
                        }
                    ],
                    value: settings.expense_taxes_calculated_by_amount,
                    group: 1
                }
            )

            fields.push(
                {
                    name: 'expenses_have_inclusive_taxes',
                    label: translations.inclusive_taxes,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.expenses_have_inclusive_taxes,
                    help_text: translations.expenses_have_inclusive_taxes,
                    group: 1
                }
            )
        }

        return [fields]
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Header title={translations.expense_settings} handleSubmit={this.handleSubmit}/>

                <div className="settings-container settings-container-narrow fixed-margin-extra">
                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getExpenseFields()}
                            />
                        </CardBody>
                    </Card>

                    <BlockButton icon={icons.percent} button_text={translations.configure_categories}
                        button_link="/#/expense_categories"/>
                </div>
            </React.Fragment>
        ) : null
    }
}
