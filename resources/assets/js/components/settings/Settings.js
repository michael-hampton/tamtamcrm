import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import {
    Card,
    CardBody,
    CardHeader,
    CustomInput,
    FormGroup,
    Label
} from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import BlockButton from '../common/BlockButton'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'
import FileUploads from '../documents/FileUploads'
import CompanyModel from '../models/CompanyModel'
import DesignFields from './DesignFields'
import EditScaffold from '../common/EditScaffold'

class Settings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: this.props.match.params.add && this.props.match.params.add === 'true' ? null : localStorage.getItem('account_id'),
            loaded: false,
            file_count: 0,
            cached_settings: {},
            settings: {},
            company_logo: null,
            activeTab: 0,
            success: false,
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
        window.addEventListener('beforeunload', this.beforeunload)
        this.getAccount()
    }

    componentWillUnmount () {
        window.removeEventListener('beforeunload', this.beforeunload)
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
        if (this.state.id === null) {
            this.setState({ loaded: true })
            return
        }

        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                file_count: response.file_count,
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
        value = value === 'true' ? true : value
        value = value === 'false' ? false : value

        const value_changed = this.state.cached_settings[name] !== value

        this.setState(prevState => ({
            changesMade: value_changed,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    handleSubmit (e) {
        this.setState({ isSaving: true })
        const url = this.state.id === null ? '/api/accounts' : `/api/accounts/${this.state.id}`

        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('company_logo', this.state.company_logo)

        if (this.state.id !== null) {
            formData.append('_method', 'PUT')
        }

        axios.post(url, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                console.log('response', response.data)
                if (this.state.id === null) {
                    this.model = new CompanyModel({ id: response.data })
                    this.model.updateSettings(response.data.settings)
                    return false
                }
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

    getAddressFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'address1',
                    label: translations.address_1,
                    type: 'text',
                    placeholder: translations.address_1,
                    value: settings.address1,
                    group: 2
                },
                {
                    name: 'address2',
                    label: translations.address_2,
                    type: 'text',
                    placeholder: translations.address_2,
                    value: settings.address2,
                    group: 2
                },
                {
                    name: 'city',
                    label: translations.city,
                    type: 'text',
                    placeholder: translations.city,
                    value: settings.city,
                    group: 2
                },
                {
                    name: 'state',
                    label: translations.town,
                    type: 'text',
                    placeholder: translations.town,
                    value: settings.state,
                    group: 2
                },
                {
                    name: 'postal_code',
                    label: translations.postcode,
                    type: 'text',
                    placeholder: translations.postcode,
                    value: settings.postal_code,
                    group: 2
                },
                {
                    name: 'country_id',
                    label: translations.country,
                    type: 'country',
                    placeholder: translations.country,
                    value: settings.country_id,
                    group: 2
                }
            ]
        ]
    }

    getFormFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'name',
                    label: translations.name,
                    type: 'text',
                    placeholder: translations.name,
                    value: settings.name,
                    group: 1
                },
                {
                    name: 'website',
                    label: translations.website,
                    type: 'text',
                    placeholder: translations.website,
                    value: settings.website,
                    group: 1
                },
                {
                    name: 'phone',
                    label: translations.phone_number,
                    type: 'text',
                    placeholder: translations.phone_number,
                    value: settings.phone,
                    group: 1
                },
                {
                    name: 'email',
                    label: translations.email,
                    type: 'text',
                    placeholder: translations.email,
                    value: settings.email,
                    group: 1
                },
                {
                    name: 'vat_number',
                    label: translations.vat_number,
                    type: 'text',
                    placeholder: translations.vat_number,
                    value: settings.vat_number,
                    group: 1
                },

                {
                    name: 'currency_id',
                    label: translations.currency,
                    type: 'currency',
                    placeholder: translations.currency,
                    value: settings.currency_id,
                    group: 3
                },
                {
                    name: 'email_style',
                    label: translations.email_style,
                    type: 'select',
                    value: settings.design,
                    group: 3,
                    options: [
                        {
                            value: 'plain',
                            text: translations.plain
                        },
                        {
                            value: 'light',
                            text: translations.light
                        },
                        {
                            value: 'dark',
                            text: translations.dark
                        },
                        {
                            value: 'custom',
                            text: translations.custom
                        }
                    ]
                },
                {
                    name: 'inclusive_taxes',
                    label: translations.inclusive_taxes,
                    type: 'select',
                    value: settings.inclusive_taxes,
                    group: 3,
                    options: [
                        {
                            value: true,
                            text: translations.yes
                        },
                        {
                            value: false,
                            text: translations.no
                        }
                    ]
                },
                {
                    name: 'charge_gateway_to_customer',
                    label: translations.charge_gateway_to_customer,
                    type: 'select',
                    value: settings.charge_gateway_to_customer,
                    group: 3,
                    options: [
                        {
                            value: true,
                            text: translations.yes
                        },
                        {
                            value: false,
                            text: translations.no
                        }
                    ]
                },
                {
                    name: 'autobilling_enabled',
                    label: translations.auto_billing_enabled,
                    type: 'switch',
                    placeholder: translations.auto_billing_enabled,
                    value: settings.auto_billing_enabled,
                    help_text: translations.auto_billing_enabled_help_text
                }
            ]
        ]
    }

    getPaymentTermFields () {
        const { settings } = this.state

        return [
            [
                {
                    name: 'payment_method_id',
                    label: translations.payment_type,
                    type: 'payment_type',
                    placeholder: translations.payment_type,
                    value: settings.payment_method_id,
                    group: 1
                },
                {
                    name: 'payment_terms',
                    label: translations.payment_terms,
                    type: 'payment_terms',
                    placeholder: translations.payment_terms,
                    value: settings.payment_terms,
                    group: 1
                },
                {
                    name: 'quote_payment_terms',
                    label: translations.quote_payment_terms,
                    type: 'payment_terms',
                    placeholder: translations.quote_payment_terms,
                    value: settings.quote_payment_terms,
                    group: 1
                }
            ]
        ]
    }

    getDesignFields () {
        const settings = this.state.settings
        const design_fields = DesignFields(settings)

        return [design_fields]
    }

    getPaymentEmailFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_send_email_for_manual_payment',
                    label: translations.should_send_email_for_manual_payment,
                    help_text: translations.should_send_email_for_manual_payment_help_text,
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    placeholder: translations.should_send_email_for_manual_payment,
                    value: settings.should_send_email_for_manual_payment,
                    class_name: 'col-12'
                },
                {
                    name: 'should_send_email_for_online_payment',
                    label: translations.should_send_email_for_online_payment,
                    help_text: translations.should_send_email_for_online_payment_help_text,
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    placeholder: translations.should_send_email_for_online_payment,
                    value: settings.should_send_email_for_online_payment,
                    class_name: 'col-12'
                }
            ]
        ]
    }

    getDefaultFields () {
        const { settings } = this.state

        const defaults = []

        const modules = JSON.parse(localStorage.getItem('modules'))

        if (modules && modules.invoices) {
            defaults.push({
                name: 'invoice_terms',
                label: translations.invoice_terms,
                type: 'textarea',
                placeholder: translations.invoice_terms,
                value: settings.invoice_terms,
                group: 1
            })
            defaults.push({
                name: 'invoice_footer',
                label: translations.invoice_footer,
                type: 'textarea',
                placeholder: translations.invoice_footer,
                value: settings.invoice_footer,
                group: 1
            })
        }

        if (modules && modules.quotes) {
            defaults.push({
                name: 'quote_terms',
                label: translations.quote_terms,
                type: 'textarea',
                placeholder: translations.quote_terms,
                value: settings.quote_terms,
                group: 1
            })

            defaults.push({
                name: 'quote_footer',
                label: translations.quote_footer,
                type: 'textarea',
                placeholder: translations.quote_footer,
                value: settings.quote_footer,
                group: 1
            })
        }

        if (modules && modules.credits) {
            defaults.push({
                name: 'credit_terms',
                label: translations.credit_terms,
                type: 'textarea',
                placeholder: translations.credit_terms,
                value: settings.credit_terms,
                group: 1
            })

            defaults.push({
                name: 'credit_footer',
                label: translations.credit_footer,
                type: 'textarea',
                placeholder: translations.credit_footer,
                value: settings.credit_footer,
                group: 1
            })
        }

        if (modules && modules.orders) {
            defaults.push({
                name: 'order_terms',
                label: translations.order_terms,
                type: 'textarea',
                placeholder: translations.order_terms,
                value: settings.order_terms,
                group: 1
            })

            defaults.push({
                name: 'order_footer',
                label: translations.order_footer,
                type: 'textarea',
                placeholder: translations.order_footer,
                value: settings.order_footer,
                group: 1
            })
        }

        const formFields = []
        formFields.push(defaults)
        return formFields
    }

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
    }

    handleClose () {
        this.setState({ success: false })
    }

    render () {
        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggle
            },
            tabs: [
                {
                    label: translations.details
                },
                {
                    label: translations.address
                },
                {
                    label: translations.logo
                },
                {
                    label: translations.defaults
                },
                {
                    label: translations.documents
                }
            ],
            children: []
        }

        tabs.children[0] = <Card>
            <CardBody>
                <FormBuilder
                    handleChange={this.handleSettingsChange}
                    formFieldsRows={this.getFormFields()}
                />
            </CardBody>
        </Card>

        tabs.children[1] = <Card>
            <CardBody>
                <FormBuilder
                    handleChange={this.handleSettingsChange}
                    formFieldsRows={this.getAddressFields()}
                />
            </CardBody>
        </Card>

        tabs.children[2] = <Card>
            <CardBody>
                <FormGroup>

                    <Label>{translations.logo}</Label>
                    <CustomInput className="mt-4 mb-4"
                        onChange={this.handleFileChange.bind(this)}
                        type="file"
                        id="company_logo" name="company_logo"
                        label="Logo"/>
                </FormGroup>
            </CardBody>
        </Card>

        tabs.children[3] = <>
            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPaymentTermFields()}
                    />

                    <BlockButton icon={icons.cog} button_text={translations.configure_payment_terms}
                        button_link="/#/payment_terms"/>
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getPaymentEmailFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getDefaultFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getDesignFields()}
                    />
                </CardBody>
            </Card>
        </>

        tabs.children[4] = <Card>
            <CardHeader>{translations.default_documents}</CardHeader>
            <CardBody>
                <FileUploads updateCount={(count) => {
                    this.setState({ file_count: count })
                }} entity_type="Account" entity={this.state}
                user_id={this.state.user_id}/>
            </CardBody>
        </Card>

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <EditScaffold isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                    title={translations.account_details}
                    isEditing={this.state.changesMade}
                    cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit}
                    tabs={tabs}/>
            </React.Fragment>
        ) : null
    }
}

export default Settings
