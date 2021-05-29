import React, {Component} from 'react'
import FormBuilder from './FormBuilder'
import {Card, CardBody, Nav, NavItem, NavLink, TabContent, TabPane} from 'reactstrap'
import axios from 'axios'
import {credit_pdf_fields} from '../models/CreditModel'
import {quote_pdf_fields} from '../models/QuoteModel'
import {invoice_pdf_fields} from '../models/InvoiceModel'
import PdfFields from './PdfFields'
import {translations} from '../utils/_translations'
import {order_pdf_fields} from '../models/OrderModel'
import {purchase_order_pdf_fields} from '../models/PurchaseOrderModel'
import {customer_pdf_fields} from '../models/CustomerModel'
import {account_pdf_fields} from '../models/AccountModel'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import {icons} from '../utils/_icons'
import BlockButton from '../common/BlockButton'
import CompanyModel from '../models/CompanyModel'
import DesignFields from './DesignFields'
import CustomFieldSettingsForm from "./CustomFieldSettingsForm";
import EditScaffold from "./EditScaffold";

class InvoiceSettings extends Component {
    constructor(props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            activeTab: 0,
            success: false,
            error: false,
            changesMade: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleColumnChange = this.handleColumnChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.toggle = this.toggle.bind(this)

        this.model = new CompanyModel({id: this.state.id})
        this.modules = JSON.parse(localStorage.getItem('modules'))
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

    toggle(event, tab) {
        if (this.state.activeTab !== tab) {
            this.setState({activeTab: tab})
        }
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

    handleChange(event) {
        this.setState({[event.target.name]: event.target.value})
    }

    handleSettingsChange(event) {
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

    handleColumnChange(values) {
        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                pdf_variables: values
            }
        }), () => this.handleSubmit())
    }

    handleSubmit() {
        const {settings} = this.state
        const formData = new FormData()
        formData.append('settings', JSON.stringify(settings))
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
                console.error(error)
                this.setState({error: true})
            })
    }

    getSettingFields() {
        const settings = this.state.settings
        const design_fields = DesignFields(settings)

        design_fields.push(
            {
                name: 'page_size',
                label: translations.page_size,
                type: 'select',
                value: settings.page_size,
                options: [
                    {
                        value: 'A1',
                        text: 'A1'
                    },
                    {
                        value: 'A2',
                        text: 'A2'
                    },
                    {
                        value: 'A3',
                        text: 'A3'
                    },
                    {
                        value: 'A4',
                        text: 'A4'
                    },
                    {
                        value: 'A5',
                        text: 'A5'
                    },
                    {
                        value: 'A6',
                        text: 'A6'
                    }
                ],
                group: 1
            }
        )

        return [design_fields]
    }

    getInvoiceSettingFields() {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'all_pages_header',
                    label: translations.all_pages_header,
                    type: 'select',
                    value: settings.all_pages_header,
                    options: [
                        {
                            value: 'true',
                            text: translations.all_pages
                        },
                        {
                            value: 'false',
                            text: translations.first_page
                        }
                    ],
                    group: 1
                },
                {
                    name: 'all_pages_footer',
                    label: translations.all_pages_footer,
                    type: 'select',
                    value: settings.all_pages_footer,
                    options: [
                        {
                            value: 'true',
                            text: translations.all_pages
                        },
                        {
                            value: 'false',
                            text: translations.first_page
                        }
                    ],
                    group: 1
                },
                {
                    name: 'dont_display_empty_pdf_columns',
                    label: translations.hide_blank_columns,
                    type: 'select',
                    value: settings.dont_display_empty_pdf_columns,
                    options: [
                        {
                            value: 'true',
                            text: translations.yes
                        },
                        {
                            value: 'false',
                            text: translations.no
                        }
                    ],
                    group: 1
                }
            ]
        ]
    }

    getInvoiceFields() {
        return invoice_pdf_fields
    }

    getOrderFields() {
        return order_pdf_fields
    }

    getPurchaseOrderFields() {
        return purchase_order_pdf_fields
    }

    getQuoteFields() {
        return quote_pdf_fields
    }

    getCreditFields() {
        return credit_pdf_fields
    }

    getProductFields() {
        return []
    }

    getTaskFields() {
        return []
    }

    handleCancel() {
        this.setState({settings: this.state.cached_settings, changesMade: false})
    }

    handleClose() {
        this.setState({success: false, error: false})
    }

    render() {
        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggle
            },
            tabs: [
                {
                    label: translations.settings
                },
                {
                    label: translations.invoice
                },
                {
                    label: translations.customer
                },
                {
                    label: translations.account
                },
            ],
            children: []
        }

        tabs.children[0] =
            <>
                <BlockButton icon={icons.link} button_text={translations.customize_and_preview}
                             button_link="/#/designs"/>

                <Card>
                    <CardBody>
                        <FormBuilder
                            handleChange={this.handleSettingsChange}
                            formFieldsRows={this.getSettingFields()}
                        />
                    </CardBody>
                </Card>
            </>


        tabs.children[1] = <Card>
            <CardBody>
                <FormBuilder
                    handleChange={this.handleSettingsChange}
                    formFieldsRows={this.getInvoiceSettingFields()}
                />
            </CardBody>
        </Card>

        tabs.children[2] = <Card>
            <CardBody>
                <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                           section="client_details" columns={customer_pdf_fields}
                           ignored_columns={this.state.settings.pdf_variables}/>
            </CardBody>
        </Card>

        tabs.children[3] = <Card>
            <CardBody>
                <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                           section="company_details" columns={account_pdf_fields}
                           ignored_columns={this.state.settings.pdf_variables}/>
            </CardBody>
        </Card>


        if (this.modules && this.modules.invoices === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="invoice" columns={this.getInvoiceFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.invoice})
        }

        if (this.modules && this.modules.quotes === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="quote" columns={this.getQuoteFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.quote})
        }

        if (this.modules && this.modules.orders === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="order" columns={this.getOrderFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.order})
        }

        if (this.modules && this.modules.purchase_orders === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="purchase_order" columns={this.getPurchaseOrderFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.POS})
        }

        if (this.modules && this.modules.credits === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="credit" columns={this.getCreditFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.credit})
        }

        tabs.children.push(<Card>
            <CardBody>
                <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                           section="product_columns" columns={this.getProductFields()}
                           ignored_columns={this.state.settings.pdf_variables}/>
            </CardBody>
        </Card>)

        tabs.tabs.push({label: translations.product})

        if (this.modules && this.modules.tasks === true) {
            tabs.children.push(<Card>
                <CardBody>
                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                               section="task_columns" columns={this.getTaskFields()}
                               ignored_columns={this.state.settings.pdf_variables}/>
                </CardBody>
            </Card>)

            tabs.tabs.push({label: translations.task})
        }

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                                 message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                                 message={translations.settings_not_saved}/>

                <EditScaffold title={translations.invoice_settings} cancelButtonDisabled={!this.state.changesMade}
                              handleCancel={this.handleCancel.bind(this)}
                              handleSubmit={this.handleSubmit.bind(this)}
                              tabs={tabs}/>


            </React.Fragment>
        ) : null
    }
}

export default InvoiceSettings
