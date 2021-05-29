import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Alert, Card, CardBody, CardHeader } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import Snackbar from '@material-ui/core/Snackbar'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import BlockButton from '../common/BlockButton'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from "./EditScaffold";

export default class TaxSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            activeTab: '1',
            cached_settings: {},
            settings: {},
            success: false,
            error: false,
            changesMade: false
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

    toggle (tab) {
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
                    changesMade: false
                }, () => this.model.updateSettings(this.state.settings))
            })
            .catch((error) => {
                this.setState({ error: true })
            })
    }

    getTaxFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'show_transaction_fee',
                    label: translations.show_transaction_fee,
                    type: 'switch',
                    placeholder: translations.under_payments_allowed,
                    value: settings.show_transaction_fee
                },
                {
                    name: 'show_shipping_cost',
                    label: translations.show_shipping_cost,
                    type: 'switch',
                    placeholder: translations.over_payments_allowed,
                    value: settings.show_shipping_cost
                },
                {
                    name: 'show_gateway_fee',
                    label: translations.show_gateway_fee,
                    type: 'switch',
                    placeholder: translations.over_payments_allowed,
                    value: settings.show_gateway_fee
                },
                {
                    name: 'show_tax_rate1',
                    label: translations.show_tax_rate1,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_tax_rate1
                },
                {
                    name: 'show_tax_rate2',
                    label: translations.show_tax_rate2,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_tax_rate2
                },
                {
                    name: 'show_tax_rate3',
                    label: translations.show_tax_rate3,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_tax_rate3
                }
            ]
        ]
    }

    getLineItemTaxFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'show_line_item_tax_rate1',
                    label: translations.show_tax_rate1,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_line_item_tax_rate1
                },
                {
                    name: 'show_line_item_tax_rate2',
                    label: translations.show_tax_rate2,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_line_item_tax_rate2
                },
                {
                    name: 'show_line_item_tax_rate3',
                    label: translations.show_tax_rate3,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.show_line_item_tax_rate3
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
        const tabs = {
            children: []
        }

        tabs.children[0] = <>
            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getTaxFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardHeader>{translations.line_items}</CardHeader>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getLineItemTaxFields()}
                    />
                </CardBody>
            </Card>

            <BlockButton icon={icons.percent} button_text={translations.configure_rates}
                         button_link="/#/tax-rates"/>
        </>
        return this.state.loaded === true ? (
            <React.Fragment>
                <Snackbar open={this.state.success} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                    <Alert severity="success">
                        {translations.settings_saved}
                    </Alert>
                </Snackbar>

                <Snackbar open={this.state.error} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                    <Alert severity="danger">
                        {translations.settings_not_saved}
                    </Alert>
                </Snackbar>

                <EditScaffold title={translations.tax_settings} cancelButtonDisabled={!this.state.changesMade}
                              handleCancel={this.handleCancel.bind(this)}
                              handleSubmit={this.handleSubmit.bind(this)}
                              tabs={tabs}/>
            </React.Fragment>
        ) : null
    }
}
