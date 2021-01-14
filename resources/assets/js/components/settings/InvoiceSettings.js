import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import { credit_pdf_fields } from '../models/CreditModel'
import { quote_pdf_fields } from '../models/QuoteModel'
import { invoice_pdf_fields } from '../models/InvoiceModel'
import PdfFields from './PdfFields'
import { translations } from '../utils/_translations'
import { order_pdf_fields } from '../models/OrderModel'
import { purchase_order_pdf_fields } from '../models/PurchaseOrderModel'
import { customer_pdf_fields } from '../models/CustomerModel'
import { account_pdf_fields } from '../models/AccountModel'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import { icons } from '../utils/_icons'
import BlockButton from '../common/BlockButton'

class InvoiceSettings extends Component {
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
        this.handleColumnChange = this.handleColumnChange.bind(this)
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
        const value = event.target.value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleColumnChange (values) {
        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                pdf_variables: values
            }
        }), () => this.handleSubmit())
    }

    handleSubmit () {
        const { settings } = this.state
        const formData = new FormData()
        formData.append('settings', JSON.stringify(settings))
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

    getSettingFields () {
        const settings = this.state.settings

        const design_options = [
            {
                value: '1',
                text: translations.basic
            },
            {
                value: '2',
                text: translations.danger
            },
            {
                value: '3',
                text: translations.dark
            },
            {
                value: '4',
                text: translations.happy
            },
            {
                value: '5',
                text: translations.info
            },
            {
                value: '6',
                text: translations.jazzy
            },
            {
                value: '7',
                text: translations.picture
            },
            {
                value: '8',
                text: translations.secondary
            },
            {
                value: '9',
                text: translations.simple
            },
            {
                value: '11',
                text: translations.warning
            }
        ]

        return [
            [
                {
                    name: 'invoice_design_id',
                    label: 'Invoice Design',
                    type: 'select',
                    value: settings.invoice_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'case_design_id',
                    label: 'Case Design',
                    type: 'select',
                    value: settings.case_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'task_design_id',
                    label: 'Task Design',
                    type: 'select',
                    value: settings.task_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'purchase_order_design_id',
                    label: 'Purchase Order Design',
                    type: 'select',
                    value: settings.purchase_order_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'lead_design_id',
                    label: 'Lead Design',
                    type: 'select',
                    value: settings.lead_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'deal_design_id',
                    label: 'Deal Design',
                    type: 'select',
                    value: settings.deal_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'credit_design_id',
                    label: translations.credit_design,
                    type: 'select',
                    value: settings.credit_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'order_design_id',
                    label: 'Order Design',
                    type: 'select',
                    value: settings.order_design_id,
                    options: design_options,
                    group: 1
                },
                {
                    name: 'quote_design_id',
                    label: translations.quote_design,
                    type: 'select',
                    value: settings.quote_design_id,
                    options: design_options,
                    group: 1
                },
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
            ]
        ]
    }

    getInvoiceSettingFields () {
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

    getInvoiceFields () {
        return invoice_pdf_fields
    }

    getOrderFields () {
        return order_pdf_fields
    }

    getPurchaseOrderFields () {
        return purchase_order_pdf_fields
    }

    getQuoteFields () {
        return quote_pdf_fields
    }

    getCreditFields () {
        return credit_pdf_fields
    }

    getProductFields () {
        return []
    }

    getTaskFields () {
        return []
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const modules = JSON.parse(localStorage.getItem('modules'))
        const tabs = <Nav className="nav-justified setting-tabs disable-scrollbars" tabs>
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '1' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('1', e)
                    }}>
                    {translations.settings}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('2', e)
                    }}>
                    {translations.invoice}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '3' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('3', e)
                    }}>
                    {translations.customer}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '4' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('4', e)
                    }}>
                    {translations.account}
                </NavLink>
            </NavItem>

            {modules && modules.invoices &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '5' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('5', e)
                    }}>
                    {translations.invoice}
                </NavLink>
            </NavItem>
            }

            {modules && modules.quotes &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '6' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('6', e)
                    }}>
                    {translations.quote}
                </NavLink>
            </NavItem>
            }

            {modules && modules.orders &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '7' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('7', e)
                    }}>
                    {translations.order}
                </NavLink>
            </NavItem>
            }

            {modules && modules.purchase_orders &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '8' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('8', e)
                    }}>
                    {translations.POS}
                </NavLink>
            </NavItem>
            }

            {modules && modules.credits &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '9' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('9', e)
                    }}>
                    {translations.credit}
                </NavLink>
            </NavItem>
            }

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '10' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('10', e)
                    }}>
                    {translations.product}
                </NavLink>
            </NavItem>

            {modules && modules.tasks &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '11' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggle('11', e)
                    }}>
                    {translations.task}
                </NavLink>
            </NavItem>
            }
        </Nav>

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Header title={translations.invoice_settings} handleSubmit={this.handleSubmit}
                    tabs={tabs}/>

                <div className="settings-container settings-container-narrow fixed-margin-mobile">
                    <TabContent activeTab={this.state.activeTab}>
                        <TabPane tabId="1">
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
                        </TabPane>

                        <TabPane tabId="2">
                            <Card>
                                <CardBody>
                                    <FormBuilder
                                        handleChange={this.handleSettingsChange}
                                        formFieldsRows={this.getInvoiceSettingFields()}
                                    />
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="3">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="client_details" columns={customer_pdf_fields}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="4">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="company_details" columns={account_pdf_fields}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>

                        {modules && modules.invoices &&
                        <TabPane tabId="5">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="invoice" columns={this.getInvoiceFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }

                        {modules && modules.quotes &&
                        <TabPane tabId="6">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="quote" columns={this.getQuoteFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }

                        {modules && modules.orders &&
                        <TabPane tabId="7">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="order" columns={this.getOrderFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }

                        {modules && modules.purchase_orders &&
                        <TabPane tabId="8">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="purchase_order" columns={this.getPurchaseOrderFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }

                        {modules && modules.credits &&
                        <TabPane tabId="9">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="credit" columns={this.getCreditFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }

                        <TabPane tabId="10">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="product_columns" columns={this.getProductFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>

                        {modules && modules.tasks &&
                        <TabPane tabId="11">
                            <Card>
                                <CardBody>
                                    <PdfFields onChange2={this.handleColumnChange} settings={this.state.settings}
                                        section="task_columns" columns={this.getTaskFields()}
                                        ignored_columns={this.state.settings.pdf_variables}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                        }
                    </TabContent>
                </div>
            </React.Fragment>
        ) : null
    }
}

export default InvoiceSettings
