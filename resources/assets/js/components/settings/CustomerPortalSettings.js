import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Alert, Card, CardBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import Snackbar from '@material-ui/core/Snackbar'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'

export default class CustomerPortalSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            activeTab: '1',
            settings: {},
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
                this.setState({ error: true })
            })
    }

    getSettingFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'portal_terms',
                    label: translations.customer_signup_terms,
                    type: 'textarea',
                    placeholder: translations.customer_signup_terms,
                    value: settings.portal_terms,
                    inputClass: 'textarea-lg'
                },
                {
                    name: 'portal_privacy_policy',
                    label: translations.portal_privacy_policy,
                    type: 'textarea',
                    placeholder: translations.portal_privacy_policy,
                    value: settings.portal_privacy_policy,
                    inputClass: 'textarea-lg'
                },
                {
                    name: 'portal_dashboard_message',
                    label: translations.dashboard_message,
                    type: 'textarea',
                    placeholder: translations.dashboard_message,
                    value: settings.portal_dashboard_message
                }
            ]
        ]
    }

    getEnabledModuleFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'portal_documents_enabled',
                    label: translations.portal_documents_enabled,
                    type: 'switch',
                    placeholder: translations.portal_documents_enabled,
                    value: settings.portal_documents_enabled,
                    help_text: translations.portal_documents_enabled_help_text
                },
                {
                    name: 'portal_dashboard_enabled',
                    label: translations.portal_dashboard_enabled,
                    type: 'switch',
                    placeholder: translations.portal_dashboard_enabled,
                    value: settings.portal_dashboard_enabled
                    // help_text: translations.portal_dashboard_enabled_help_text
                },
                {
                    name: 'portal_tasks_enabled',
                    label: translations.portal_tasks_enabled,
                    type: 'switch',
                    placeholder: translations.portal_tasks_enabled,
                    value: settings.portal_tasks_enabled
                    // help_text: translations.portal_tasks_enabled_help_text
                },
                {
                    name: 'portal_cases_enabled',
                    label: translations.portal_cases_enabled,
                    type: 'switch',
                    placeholder: translations.portal_cases_enabled,
                    value: settings.portal_cases_enabled
                    // help_text: translations.portal_tasks_enabled_help_text
                }
            ]
        ]
    }

    getBillingFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'under_payments_allowed',
                    label: translations.under_payments_allowed,
                    type: 'switch',
                    placeholder: translations.under_payments_allowed,
                    value: settings.under_payments_allowed,
                    help_text: translations.under_payments_allowed_help_text
                },
                {
                    name: 'over_payments_allowed',
                    label: translations.over_payments_allowed,
                    type: 'switch',
                    placeholder: translations.over_payments_allowed,
                    value: settings.over_payments_allowed,
                    help_text: translations.over_payments_allowed_help_text
                },
                {
                    name: 'minimum_amount_required',
                    label: translations.minimum_amount_required,
                    type: 'text',
                    placeholder: translations.minimum_amount_required,
                    value: settings.minimum_amount_required
                },
                {
                    name: 'credit_payments_enabled',
                    label: translations.credit_payments_enabled,
                    type: 'switch',
                    placeholder: translations.credit_payments_enabled,
                    value: settings.credit_payments_enabled,
                    help_text: translations.over_payments_allowed_help_text
                }
            ]
        ]
    }

    getSecurityFields () {
        const settings = this.state.settings

        return [
            [
                {
                    icon: `fa ${icons.shield}`,
                    name: 'require_customer_portal_login',
                    label: translations.enable_portal_password,
                    type: 'switch',
                    placeholder: translations.enable_portal_password_help,
                    value: settings.require_customer_portal_login,
                    help_text: translations.customer_registration_help_text
                },
                {
                    name: 'display_invoice_terms',
                    label: translations.display_invoice_terms,
                    icon: `fa ${icons.checkbox_o}`,
                    type: 'switch',
                    placeholder: translations.display_invoice_terms,
                    value: settings.display_invoice_terms
                },
                {
                    name: 'display_quote_terms',
                    label: translations.display_quote_terms,
                    icon: `fa ${icons.checkbox_o}`,
                    type: 'switch',
                    placeholder: translations.display_quote_terms,
                    value: settings.display_quote_terms
                },
                {
                    name: 'display_invoice_signature',
                    label: translations.display_invoice_signature,
                    icon: `fa ${icons.pencil}`,
                    type: 'switch',
                    placeholder: translations.display_invoice_signature,
                    value: settings.display_invoice_signature
                },
                {
                    name: 'display_quote_signature',
                    label: translations.display_quote_signature,
                    icon: `fa ${icons.pencil}`,
                    type: 'switch',
                    placeholder: translations.display_quote_signature,
                    value: settings.display_quote_signature
                }
            ]
        ]
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const tabs = <Nav tabs className="nav-justified setting-tabs disable-scrollbars">
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '1' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('1')
                    }}>
                    {translations.settings}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('2')
                    }}>
                    {translations.security}
                </NavLink>
            </NavItem>

            {/* <NavItem> */}
            {/*    <NavLink */}
            {/*        className={this.state.activeTab === '3' ? 'active' : ''} */}
            {/*        onClick={() => { */}
            {/*            this.toggle('3') */}
            {/*        }}> */}
            {/*        {translations.billing} */}
            {/*    </NavLink> */}
            {/* </NavItem> */}
        </Nav>

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

                <Header tabs={tabs} title={translations.customer_portal}
                    handleSubmit={this.handleSubmit.bind(this)}/>

                <div className="settings-container settings-container-narrow fixed-margin-mobile">
                    <TabContent activeTab={this.state.activeTab}>
                        <TabPane tabId="1">
                            <Card className="mb-2">
                                <CardBody>
                                    <FormBuilder
                                        handleChange={this.handleSettingsChange}
                                        formFieldsRows={this.getEnabledModuleFields()}
                                    />
                                </CardBody>
                            </Card>

                            <Card>
                                <CardBody>
                                    <FormBuilder
                                        handleChange={this.handleSettingsChange}
                                        formFieldsRows={this.getSettingFields()}
                                    />
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="2">
                            <Card>
                                <CardBody>
                                    <FormBuilder
                                        handleChange={this.handleSettingsChange}
                                        formFieldsRows={this.getSecurityFields()}
                                    />
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="3">
                            <Card>
                                <CardBody>
                                    <FormBuilder
                                        handleChange={this.handleSettingsChange}
                                        formFieldsRows={this.getBillingFields()}
                                    />
                                </CardBody>
                            </Card>
                        </TabPane>
                    </TabContent>
                </div>
            </React.Fragment>
        ) : null
    }
}
