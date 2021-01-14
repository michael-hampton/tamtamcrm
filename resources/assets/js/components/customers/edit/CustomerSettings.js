import React, { Component } from 'react'
import FormBuilder from '../../settings/FormBuilder'
import { Button, Card, CardBody, CardHeader, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import CustomerModel from '../../models/CustomerModel'
import { icons } from '../../utils/_icons'
import { consts } from '../../utils/_consts'

class CustomerSettings extends Component {
    constructor (props) {
        super(props)
        this.state = {
            id: this.props.customer.id,
            activeTab: '1',
            settings: this.props.customer.settings,
            loading: false,
            changesMade: false,
            errors: []
        }

        this.customerModel = new CustomerModel(this.props.customer)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleClick = this.handleClick.bind(this)
    }

    toggleTab (tab, e) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }

        const parent = e.currentTarget.parentNode
        const rect = parent.getBoundingClientRect()
        const rect2 = parent.nextSibling.getBoundingClientRect()
        const rect3 = parent.previousSibling.getBoundingClientRect()
        const winWidth = window.innerWidth || document.documentElement.clientWidth
        const widthScroll = winWidth * 33 / 100
        const diff = window.innerWidth <= 768 ? 10 : 400

        if (rect.left <= diff || rect3.left <= diff) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft -= widthScroll
        }

        if (rect.right >= winWidth - diff || rect2.right >= winWidth - diff) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft += widthScroll
        }
    }

    handleSettingsChange (event) {
        const name = event.target.name
        let value = event.target.value
        value = value === 'true' ? true : value
        value = value === 'false' ? false : value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }


getCustomerPortalFields () {
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
                },
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
                },
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

    getFormFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'language_id',
                    label: translations.language,
                    type: 'language',
                    placeholder: translations.language,
                    value: settings.language_id,
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
                    help_text: translations.auto_billing_enabled_help_text,
                    class_name: 'col-12'
                },
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

        return formFields
    }

    getDefaultFields () {
        const { settings } = this.state
        const formFields = [
            [
                {
                    name: 'payment_terms',
                    label: translations.payment_terms,
                    type: 'payment_terms',
                    placeholder: translations.payment_terms,
                    value: settings.payment_terms,
                    group: 1
                },
                {
                    name: 'payment_type_id',
                    label: translations.payment_type,
                    type: 'payment_type',
                    placeholder: translations.payment_type,
                    value: settings.payment_type_id,
                    group: 1
                },
                {
                    name: 'invoice_terms',
                    label: translations.invoice_terms,
                    type: 'textarea',
                    placeholder: translations.invoice_terms,
                    value: settings.invoice_terms,
                    group: 1
                },
                {
                    name: 'invoice_footer',
                    label: translations.invoice_footer,
                    type: 'textarea',
                    placeholder: translations.invoice_footer,
                    value: settings.invoice_footer,
                    group: 1
                },
                {
                    name: 'quote_terms',
                    label: translations.quote_terms,
                    type: 'textarea',
                    placeholder: translations.quote_terms,
                    value: settings.quote_terms,
                    group: 1
                },
                {
                    name: 'quote_footer',
                    label: translations.quote_footer,
                    type: 'textarea',
                    placeholder: translations.quote_footer,
                    value: settings.quote_footer,
                    group: 1
                },
                {
                    name: 'credit_terms',
                    label: translations.credit_terms,
                    type: 'textarea',
                    placeholder: translations.credit_terms,
                    value: settings.credit_terms,
                    group: 1
                },
                {
                    name: 'credit_footer',
                    label: translations.credit_footer,
                    type: 'textarea',
                    placeholder: translations.credit_footer,
                    value: settings.credit_footer,
                    group: 1
                },
                {
                    name: 'order_terms',
                    label: translations.order_terms,
                    type: 'textarea',
                    placeholder: translations.order_terms,
                    value: settings.order_terms,
                    group: 1
                },
                {
                    name: 'order_footer',
                    label: translations.order_footer,
                    type: 'textarea',
                    placeholder: translations.order_footer,
                    value: settings.order_footer,
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getDealFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'should_email_deal',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_deal,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_deal',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_deal,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_convert_deal',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_deal,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getCaseFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'default_case_priority',
                    label: translations.default_case_priority,
                    icon: `fa ${icons.envelope}`,
                    type: 'select',
                    options: [
                        {
                            value: consts.low_priority,
                            text: translations.low
                        },
                        {
                            value: consts.medium_priority,
                            text: translations.medium
                        },
                        {
                            value: consts.high_priority,
                            text: translations.high
                        }
                    ],
                    value: settings.default_case_priority,
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getPaymentFields () {
        const settings = this.state.settings

        const formFields = [
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

        return formFields
    }

    getInvoiceFields () {
        const settings = this.state.settings

        const formFields = [
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
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_invoice',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_invoice,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getOrderFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'should_email_order',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_order,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_order',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_order,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_convert_order',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_order,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getLeadFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'should_email_lead',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_lead,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_lead',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_lead,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_convert_lead',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_lead,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getQuoteFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'should_email_quote',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_quote,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_quote',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_quote,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_convert_quote',
                    label: 'Auto Convert',
                    icon: `fa ${icons.book}`,
                    type: 'switch',
                    value: settings.should_convert_quote,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getPurchaseOrderFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'should_email_purchase_order',
                    label: 'Auto Email',
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.should_email_purchase_order,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'should_archive_purchase_order',
                    label: 'Auto Archive',
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.should_archive_purchase_order,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getExpenseFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'create_expense_invoice',
                    label: translations.create_expense_invoice,
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.create_expense_invoice,
                    help_text: translations.create_expense_invoice_help,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'include_expense_documents',
                    label: translations.include_expense_documents,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.include_expense_documents,
                    help_text: translations.include_expense_documents_help,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'create_expense_payment',
                    label: translations.create_expense_payment,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.create_expense_payment,
                    help_text: translations.create_expense_payment_help,
                    class_name: 'col-12',
                    group: 1
                },
                {
                    name: 'convert_expense_currency',
                    label: translations.convert_expense_currency,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.convert_expense_currency,
                    help_text: translations.convert_expense_currency_help,
                    class_name: 'col-12',
                    group: 1
                }
            ]
        ]

        return formFields
    }

    getInvoiceNumberFields () {
        const settings = this.state.settings

        console.log('settings', settings)

        const formFields = [
            [
                {
                    name: 'invoice_number_pattern',
                    label: 'Invoice Number Pattern',
                    type: 'text',
                    placeholder: 'Invoice Number Pattern',
                    value: settings.invoice_number_pattern,
                    group: 1
                },
                {
                    name: 'invoice_number_counter',
                    label: 'Invoice Counter',
                    type: 'text',
                    placeholder: 'Invoice Counter',
                    value: settings.invoice_number_counter
                }
            ]
        ]

        return formFields
    }

    getOrderNumberFields () {
        const settings = this.state.settings

        console.log('settings', settings)

        const formFields = [
            [
                {
                    name: 'order_number_pattern',
                    label: 'Order Number Pattern',
                    type: 'text',
                    placeholder: 'Order Number Pattern',
                    value: settings.order_number_pattern,
                    group: 1
                },
                {
                    name: 'order_number_counter',
                    label: 'Order Counter',
                    type: 'text',
                    placeholder: 'Order Counter',
                    value: settings.order_number_counter
                }
            ]
        ]

        return formFields
    }

    getQuoteNumberFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'quote_number_pattern',
                    label: 'Quote Number Pattern',
                    type: 'text',
                    placeholder: 'Quote Number Pattern',
                    value: settings.quote_number_pattern,
                    group: 1
                },
                {
                    name: 'quote_number_counter',
                    label: 'Quote Counter',
                    type: 'text',
                    placeholder: 'Quote Counter',
                    value: settings.quote_number_counter
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

        return formFields
    }

    getCreditNumberFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'credit_number_pattern',
                    label: 'Credit Number Pattern',
                    type: 'text',
                    placeholder: 'Credit Number Pattern',
                    value: settings.credit_number_pattern,
                    group: 1
                },
                {
                    name: 'credit_number_counter',
                    label: 'Credit Counter',
                    type: 'text',
                    placeholder: 'Credit Counter',
                    value: settings.credit_number_counter
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

        return formFields
    }

    getPaymentNumberFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'payment_number_counter',
                    label: 'Payment Counter',
                    type: 'text',
                    placeholder: 'Payment Counter',
                    value: settings.payment_number_counter
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

        return formFields
    }

    getProjectNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'project_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.project_number_pattern,
                    group: 1
                },
                {
                    name: 'project_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.project_number_counter
                }
            ]
        ]
    }

    getExpenseNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'expense_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.expense_number_pattern,
                    group: 1
                },
                {
                    name: 'expense_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.expense_number_counter
                }
            ]
        ]
    }

    getCompanyNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'company_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.company_number_pattern,
                    group: 1
                },
                {
                    name: 'company_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.company_number_counter
                }
            ]
        ]
    }

    getPurchaseOrderNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'purchaseorder_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.purchaseorder_number_pattern,
                    group: 1
                },
                {
                    name: 'purchaseorder_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.purchaseorder_number_counter
                }
            ]
        ]
    }

    getDealNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'deal_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.deal_number_pattern,
                    group: 1
                },
                {
                    name: 'deal_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.deal_number_counter
                }
            ]
        ]
    }

    getCaseNumberFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'case_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.case_number_pattern,
                    group: 1
                },
                {
                    name: 'case_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.case_number_counter
                }
            ]
        ]

        return formFields
    }

    getTaskNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'task_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.task_number_pattern,
                    group: 1
                },
                {
                    name: 'task_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.task_number_counter
                }
            ]
        ]
    }

    getRecurringInvoiceNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'recurringinvoice_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.recurringinvoice_number_pattern,
                    group: 1
                },
                {
                    name: 'recurringinvoice_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.recurringinvoice_number_counter
                }
            ]
        ]
    }

    getRecurringQuoteNumberFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'recurringquote_number_pattern',
                    label: translations.number_pattern,
                    type: 'text',
                    placeholder: translations.number_pattern,
                    value: settings.recurringquote_number_pattern,
                    group: 1
                },
                {
                    name: 'recurringquote_number_counter',
                    label: translations.number_counter,
                    type: 'text',
                    placeholder: translations.number_counter,
                    value: settings.recurringquote_number_counter
                }
            ]
        ]
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value,
            changesMade: true
        })
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        this.customerModel.save({
            settings: this.state.settings,
            name: this.customerModel.fields.name
        }).then(response => {
            if (!response) {
                this.setState({ errors: this.customerModel.errors, message: this.customerModel.error_message })
                return
            }
            alert('good')
        })
    }

    render () {
        const modules = JSON.parse(localStorage.getItem('modules'))

        return (
            <React.Fragment>
                <Nav tabs className="nav-justified setting-tabs disable-scrollbars">
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '1' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('1', e)
                            }}>
                            {translations.details}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('2', e)
                            }}>
                            {translations.defaults}
                        </NavLink>
                    </NavItem>

                    {modules && modules.invoices &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('3', e)
                            }}>
                            {translations.invoices}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.quotes &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '4' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('4', e)
                            }}>
                            {translations.quotes}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.leads &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '5' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('5', e)
                            }}>
                            {translations.leads}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.orders &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '6' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('6', e)
                            }}>
                            {translations.orders}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.credits &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '7' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('7', e)
                            }}>
                            {translations.credits}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.payments &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '8' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('8', e)
                            }}>
                            {translations.payments}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.deals &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '9' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('9', e)
                            }}>
                            {translations.deals}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.cases &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '10' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('10', e)
                            }}>
                            {translations.cases}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.purchase_orders &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '11' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('11', e)
                            }}>
                            {translations.pos}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.recurringInvoices &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '12' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('12', e)
                            }}>
                            {translations.recurring_invoices}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.recurringQuotes &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '13' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('13', e)
                            }}>
                            {translations.recurring_quotes}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.tasks &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '14' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('14', e)
                            }}>
                            {translations.tasks}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.expenses &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '15' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('15', e)
                            }}>
                            {translations.expenses}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.projects &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '16' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('16', e)
                            }}>
                            {translations.projects}
                        </NavLink>
                    </NavItem>
                    }

                    {modules && modules.companies &&
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '17' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('17', e)
                            }}>
                            {translations.companies}
                        </NavLink>
                    </NavItem>
                    }

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '18' ? 'active' : ''}
                            onClick={(e) => {
                                this.toggleTab('18', e)
                            }}>
                            {translations.customer_portal}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Card>
                            <CardHeader>{translations.details}</CardHeader>
                            <CardBody>

                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getFormFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="2">
                        <Card>
                            <CardHeader>{translations.defaults}</CardHeader>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getDefaultFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="3">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getInvoiceFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getInvoiceNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="4">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getQuoteFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getQuoteNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="5">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getLeadFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="6">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getOrderFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getOrderNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="7">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getCreditNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="8">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getPaymentFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getPaymentNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="9">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getDealFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getDealNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="10">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getCaseFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getCaseNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="11">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getPurchaseOrderFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getPurchaseOrderNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="12">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getRecurringInvoiceNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="13">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getRecurringQuoteNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="14">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getTaskNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="15">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getExpenseFields()}
                                />
                            </CardBody>
                        </Card>

                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getExpenseNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="16">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getProjectNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="17">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getCompanyNumberFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="18">
                        <Card>
                            <CardBody>
                                <FormBuilder
                                    handleChange={this.handleSettingsChange}
                                    formFieldsRows={this.getCustomerPortalFields()}
                                />
                            </CardBody>
                        </Card>
                    </TabPane>

                    <Button color="primary" onClick={this.handleClick}>{translations.save}</Button>
                </TabContent>
            </React.Fragment>
        )
    }
}

export default CustomerSettings
