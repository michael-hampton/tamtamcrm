import React, {Component} from 'react'
import axios from 'axios'
import {
    Card,
    CardBody,
    CustomInput,
    Form,
    FormGroup,
    Label
} from 'reactstrap'
import {translations} from '../utils/_translations'
import BlockButton from '../common/BlockButton'
import {icons} from '../utils/_icons'
import SnackbarMessage from '../common/SnackbarMessage'
import CompanyModel from '../models/CompanyModel'
import AccountRepository from '../repositories/AccountRepository'
import FormBuilder from './FormBuilder'
import ConfirmPassword from '../common/ConfirmPassword'
import UpgradeAccount from './UpgradeAccount'
import ApplyLicence from './ApplyLicence'
import EditScaffold from "../common/EditScaffold";

class ModuleSettings extends Component {
    constructor(props) {
        super(props)
        this.state = {
            loaded: false,
            id: localStorage.getItem('account_id'),
            success_message: translations.settings_saved,
            activeTab: 0,
            cached_settings: {},
            settings: {},
            success: false,
            error: false,
            changesMade: false,
            isSaving: false,
            showConfirm: false,
            modules: Object.prototype.hasOwnProperty.call(localStorage, 'modules') ? JSON.parse(localStorage.getItem('modules')) : {
                recurringInvoices: false,
                recurringQuotes: false,
                purchaseOrders: false,
                promocodes: false,
                credits: false,
                orders: false,
                leads: false,
                deals: false,
                products: false,
                invoices: false,
                payments: false,
                quotes: false,
                expenses: false,
                events: false,
                customers: true,
                companies: true,
                projects: false,
                cases: false,
                tasks: false,
                recurringExpenses: false,
                recurringTasks: false
            },
            moduleTypes: [
                {
                    id: 'recurringInvoices',
                    value: 1,
                    label: translations.recurring_invoices,
                    isChecked: false
                },
                {
                    id: 'recurringQuotes',
                    value: 1,
                    label: translations.recurring_quotes,
                    isChecked: false
                },
                {
                    id: 'purchaseOrders',
                    value: 1,
                    label: translations.purchase_orders,
                    isChecked: false
                },
                {
                    id: 'credits',
                    value: 2,
                    label: translations.credits,
                    isChecked: false
                },
                {
                    id: 'quotes',
                    value: 4,
                    label: translations.quotes,
                    isChecked: false
                },
                {
                    id: 'products',
                    value: 4,
                    label: translations.products,
                    isChecked: false
                },
                {
                    id: 'leads',
                    value: 4,
                    label: translations.leads,
                    isChecked: false
                },
                {
                    id: 'events',
                    value: 4,
                    label: translations.events,
                    isChecked: false
                },
                {
                    id: 'deals',
                    value: 4,
                    label: translations.deals,
                    isChecked: false
                },
                {id: 'tasks', value: 8, label: 'Tasks', isChecked: false},
                {
                    id: 'expenses',
                    value: 16,
                    label: translations.expenses,
                    isChecked: false
                },
                {
                    id: 'projects',
                    value: 32,
                    label: translations.projects,
                    isChecked: false
                },
                {
                    id: 'companies',
                    value: 64,
                    label: translations.companies,
                    isChecked: false
                },
                {
                    id: 'cases',
                    value: 128,
                    label: translations.cases,
                    isChecked: false
                },
                {
                    id: 'recurringExpenses',
                    value: 512,
                    label: translations.recurring_expenses,
                    isChecked: false
                },
                {
                    id: 'recurringTasks',
                    value: 1024,
                    label: 'Recurring Tasks',
                    isChecked: false
                },
                {
                    id: 'tasks',
                    value: 1024,
                    label: translations.tasks,
                    isChecked: false
                },
                {
                    id: 'payments',
                    value: 1024,
                    label: translations.payments,
                    isChecked: false
                },
                {
                    id: 'invoices',
                    value: 1024,
                    label: translations.invoices,
                    isChecked: false
                },
                {
                    id: 'orders',
                    value: 2000,
                    label: translations.orders,
                    isChecked: false
                },
                {
                    id: 'promocodes',
                    value: 2000,
                    label: translations.promocodes,
                    isChecked: false
                }
            ]
        }

        this.deleteAccount = this.deleteAccount.bind(this)
        this.customInputSwitched = this.customInputSwitched.bind(this)
        this.handleAllChecked = this.handleAllChecked.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.backupData = this.backupData.bind(this)

        this.model = new CompanyModel({id: this.state.id})
    }

    componentDidMount() {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getAccount()
    }

    componentWillUnmount() {
        window.removeEventListener('beforeunload', this.beforeunload.bind(this))
    }

    beforeunload(e) {
        if (this.state.changesMade) {
            if (!confirm(translations.changes_made_warning)) {
                e.preventDefault()
                return false
            }
        }
    }

    backupData() {
        const accountRepository = new AccountRepository()

        accountRepository.backupData().then(response => {
            if (!response) {
                this.setState({error: true})
                return
            }

            this.setState({success: true, success_message: translations.account_export_completed})
        })
    }

    handleSubmit(e) {
        this.setState({isSaving: true})
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
                this.setState({error: true})
            })
    }

    getAccount() {
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

    handleSettingsChange(event) {
        const name = event.target.name
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value

        this.setState(prevState => ({
            changesMade: true,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    getSettingFields() {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'require_admin_password',
                    label: translations.require_admin_password,
                    type: 'switch',
                    value: settings.require_admin_password
                },
                {
                    name: 'default_logout_time',
                    label: translations.default_logout_time,
                    type: 'select',
                    options: [
                        {
                            value: '480',
                            text: `8 ${translations.hours}`
                        },
                        {
                            value: '1440',
                            text: `1 ${translations.day}`
                        },
                        {
                            value: '10080',
                            text: `7 ${translations.days}`
                        },
                        {
                            value: '43200',
                            text: `30 ${translations.days}`
                        }
                    ],
                    value: settings.default_logout_time,
                    group: 1
                }
            ]
        ]
    }

    toggleTab(event, tab) {
        if (this.state.activeTab !== tab) {
            this.setState({activeTab: tab})
        }
    }

    deleteAccount(id, password) {
        if (!password.trim().length) {
            this.setState({error: true})
            return false
        }

        const url = `/api/account/${this.state.id}`
        axios.delete(url, {password: password})
            .then((r) => {
                this.setState({
                    showConfirm: false
                })
                alert('The account has been deleted')
                location.href = '/Login#/login'
            })
            .catch((e) => {
                this.setState({error: true})
            })
    }

    handleAllChecked(event) {
        const modules = this.state.modules
        Object.keys(modules).forEach(module => modules[module] = event.target.checked)
        this.setState({modules: modules}, () => localStorage.setItem('modules', JSON.stringify(this.state.modules)))
    }

    customInputSwitched(buttonName, e) {
        const name = e.target.id
        const checked = e.target.checked

        this.setState(prevState => ({
            modules: {
                ...prevState.modules,
                [name]: checked
            }
        }), () => localStorage.setItem('modules', JSON.stringify(this.state.modules)))
    }

    handleClose() {
        this.setState({success: false, error: false, success_message: translations.settings_saved})
    }

    handleCancel() {
        this.setState({settings: this.state.cached_settings, changesMade: false})
    }

    render() {
        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggleTab
            },
            tabs: [
                {
                    label: translations.overview
                },
                {
                    label: translations.enable_modules
                },
                {
                    label: translations.security
                }
            ],
            children: []
        }

        tabs.children[0] = <Card>
            <CardBody>
                <div className="d-flex justify-content-between">
                    <UpgradeAccount callback={(e) => {
                        console.log('upgrade', e)
                    }}/>
                    <ApplyLicence callback={(e) => {
                        console.log('apply', e)
                    }}/>
                </div>

                <BlockButton icon={icons.link} button_text={translations.webhooks}
                             button_link="/#/subscriptions"/>
                <BlockButton icon={icons.token} button_text={translations.tokens}
                             button_link="/#/tokens"/>

                <ConfirmPassword id={this.state.id} callback={(id, password) => {
                    this.deleteAccount(id, password)
                }
                } text={translations.delete_account_message} icon={icons.delete}
                                 button_color="btn-danger btn-lg btn-block"
                                 button_label={translations.delete_account} icon_style={{
                    transform: 'rotate(20deg)',
                    marginRight: '14px',
                    fontSize: '24px'
                }}/>

                <BlockButton icon={icons.download} button_text={translations.export}
                             onClick={this.backupData}/>
            </CardBody>
        </Card>

        tabs.children[1] = <Card>
            <CardBody>
                <Form>
                    <FormGroup>
                        <Label for="exampleCheckbox">Switches <input type="checkbox"
                                                                     onClick={this.handleAllChecked}/>Check
                            all </Label>
                        {this.state.moduleTypes.map((module, index) => {
                                const isChecked = this.state.modules[module.id]

                                return (
                                    <div key={index}>
                                        <CustomInput
                                            checked={isChecked}
                                            type="switch"
                                            id={module.id}
                                            name="customSwitch"
                                            label={module.label}
                                            onChange={this.customInputSwitched.bind(this, module.value)}
                                        />
                                    </div>
                                )
                            }
                        )}
                    </FormGroup>
                </Form>
            </CardBody>
        </Card>

        tabs.children[2] = <Card>
            <CardBody>
                <FormBuilder
                    handleChange={this.handleSettingsChange}
                    formFieldsRows={this.getSettingFields()}
                />
            </CardBody>
        </Card>

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                                 message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                                 message={translations.settings_not_saved}/>

                <EditScaffold isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                              isEditing={this.state.changesMade} fullWidth={true}
                              title={translations.account_management}
                              cancelButtonDisabled={!this.state.changesMade}
                              handleCancel={this.handleCancel.bind(this)}
                              handleSubmit={this.handleSubmit.bind(this)}
                              tabs={tabs}/>

            </React.Fragment>

        )
    }
}

export default ModuleSettings
