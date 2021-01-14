import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import FormBuilder from './FormBuilder'
import { translations } from '../utils/_translations'
import { consts, frequencyOptions } from '../utils/_consts'

class EmailFields extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            loaded: false,
            activeTab: '1',
            company_logo: null,
            preview: []
        }
    }

    getFormFields (key = null) {
        const settings = this.props.settings
        const frequencies = []

        Object.keys(frequencyOptions).map((frequency) => {
            console.log('frequency', frequency)
            frequencies.push(
                {
                    value: frequency,
                    text: translations[frequencyOptions[frequency]]
                }
            )
        })

        const formFields = {
            email_template_invoice: {
                name: translations.invoice,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_invoice',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_invoice,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_invoice',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_invoice,
                        group: 1
                    }
                ]
            },

            email_template_payment: {
                name: translations.payment,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_payment',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_payment,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_payment',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_payment,
                        group: 1
                    }
                ]
            },
            email_template_statement: {
                name: 'Statement',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_statement',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_statement,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_statement',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_statement,
                        group: 1
                    }
                ]
            },
            email_template_payment_partial: {
                name: 'Partal Payment',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_payment_partial',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_payment_partial,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_payment_partial',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_payment_partial,
                        group: 1
                    }
                ]
            },
            email_template_quote: {
                name: translations.quote,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_quote',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_quote,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_quote',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_quote,
                        group: 1
                    }
                ]
            },
            email_template_credit: {
                name: translations.credit,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_credit',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_credit,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_credit',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_credit,
                        group: 1
                    }
                ]
            },
            email_template_lead: {
                name: translations.lead,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_lead',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_lead,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_lead',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_lead,
                        group: 1
                    }
                ]
            },
            email_template_deal: {
                name: translations.deal,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_deal',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_deal,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_deal',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_deal,
                        group: 1
                    }
                ]
            },
            email_template_task: {
                name: translations.task,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_task',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_task,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_task',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_task,
                        group: 1
                    }
                ]
            },
            email_template_case: {
                name: translations.cases,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_case',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_case,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_case',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_case,
                        group: 1
                    }
                ]
            },
            email_template_purchase_order: {
                name: translations.purchase_order,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_purchase_order',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_purchase_order,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_purchase_order',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_purchase_order,
                        group: 1
                    }
                ]
            },
            email_template_order_received: {
                name: 'Order Received',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_order_received',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_order_received,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_order_received',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_order_received,
                        group: 1
                    }
                ]
            },
            email_template_order_sent: {
                name: 'Order Sent',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_order_sent',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_order_sent,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_order_sent',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_order_sent,
                        group: 1
                    }
                ]
            },
            email_template_reminder_endless: {
                name: 'Endless',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_reminder_endless',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_reminder_endless,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'endless_reminder_message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.endless_reminder_message,
                        group: 1
                    },
                    {
                        id: 'endless_reminder_frequency_id',
                        name: 'endless_reminder_frequency_id',
                        label: translations.schedule,
                        type: 'select',
                        options: frequencies,
                        value: settings.endless_reminder_frequency_id
                    },
                    {
                        id: 'amount_to_charge_endless',
                        name: 'amount_to_charge_endless',
                        label: translations.late_fee_amount,
                        type: 'text',
                        placeholder: translations.late_fee_amount,
                        value: settings.amount_to_charge_endless,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'percent_to_charge_endless',
                        label: 'Fee Percent',
                        type: 'text',
                        placeholder: 'Fee Percent',
                        value: settings.percent_to_charge_endless,
                        group: 1
                    }
                ]
            },
            email_template_custom1: {
                name: 'Custom 1',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_custom1',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_custom1,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_custom1',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_custom1,
                        group: 1
                    }
                ]
            },
            email_template_custom2: {
                name: 'Custom 2',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_custom2',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_custom2,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_custom2',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_custom2,
                        group: 1
                    }
                ]
            },
            email_template_custom3: {
                name: 'Custom 3',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'email_subject_custom3',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_custom3,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_custom3',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_custom3,
                        group: 1
                    }
                ]
            },
            email_template_reminder1: {
                name: 'Reminder 1',
                is_reminder: true,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'reminder1_subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.reminder1_subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'reminder1_message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.reminder1_message,
                        group: 1
                    },
                    {
                        id: 'number_of_days_after_1',
                        name: 'number_of_days_after_1',
                        label: translations.days,
                        type: 'text',
                        placeholder: translations.days,
                        value: settings.number_of_days_after_1,
                        group: 1
                    },
                    {
                        id: 'scheduled_to_send_1',
                        name: 'schedule_reminder1',
                        label: translations.schedule,
                        type: 'select',
                        options: [
                            {
                                value: consts.reminder_schedule_after_invoice_date,
                                text: translations.after_invoice_date
                            },
                            {
                                value: consts.reminder_schedule_before_due_date,
                                text: translations.before_due_date
                            },
                            {
                                value: consts.reminder_schedule_after_due_date,
                                text: translations.after_due_date
                            }
                        ],
                        value: settings.schedule_reminder1
                    },
                    {
                        id: 'amount_to_charge_1',
                        name: 'amount_to_charge_1',
                        label: translations.late_fee_amount,
                        type: 'text',
                        placeholder: translations.late_fee_amount,
                        value: settings.amount_to_charge_1,
                        group: 1
                    },
                    {
                        id: 'percent_to_charge_1',
                        name: 'percent_to_charge_1',
                        label: translations.late_fee_percent,
                        type: 'text',
                        placeholder: translations.percent_to_charge_1,
                        value: settings.percent_to_charge_1,
                        group: 1
                    },
                    {
                        id: 'reminder1_enabled',
                        name: 'reminder1_enabled',
                        label: translations.send_email,
                        type: 'switch',
                        placeholder: translations.send_email,
                        value: settings.reminder1_enabled,
                        group: 1
                    }
                ]
            },
            email_template_reminder2: {
                name: 'Reminder 2',
                is_reminder: true,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'reminder2_subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_reminder2,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'reminder2_message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: settings.email_template_reminder2,
                        group: 1
                    },
                    {
                        id: 'number_of_days_after_2',
                        name: 'number_of_days_after_2',
                        label: translations.days,
                        type: 'text',
                        placeholder: translations.days,
                        value: settings.number_of_days_after_2,
                        group: 1
                    },
                    {
                        id: 'scheduled_to_send_2',
                        name: 'scheduled_to_send_2',
                        label: translations.schedule,
                        type: 'select',
                        options: [
                            {
                                value: consts.reminder_schedule_after_invoice_date,
                                text: translations.after_invoice_date
                            },
                            {
                                value: consts.reminder_schedule_before_due_date,
                                text: translations.before_due_date
                            },
                            {
                                value: consts.reminder_schedule_after_due_date,
                                text: translations.after_due_date
                            }
                        ],
                        value: settings.scheduled_to_send_2
                    },
                    {
                        id: 'amount_to_charge_2',
                        name: 'amount_to_charge_2',
                        label: translations.late_fee_amount,
                        type: 'text',
                        placeholder: translations.late_fee_amount,
                        value: settings.amount_to_charge_2,
                        group: 1
                    },
                    {
                        id: 'percent_to_charge_2',
                        name: 'percent_to_charge_2',
                        label: translations.late_fee_percent,
                        type: 'text',
                        placeholder: translations.late_fee_percent,
                        value: settings.percent_to_charge_2,
                        group: 1
                    },
                    {
                        id: 'reminder2_enabled',
                        name: 'reminder2_enabled',
                        label: translations.send_email,
                        type: 'switch',
                        placeholder: 'Send Email',
                        value: settings.reminder2_enabled,
                        group: 1
                    }
                ]
            },
            email_template_reminder3: {
                name: 'Reminder 3',
                is_reminder: true,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'reminder3_subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: settings.email_subject_reminder3,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'reminder3_message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: 'Website',
                        value: settings.email_template_reminder3,
                        group: 1
                    },
                    {
                        id: 'number_of_days_after_3',
                        name: 'number_of_days_after_3',
                        label: translations.days,
                        type: 'text',
                        placeholder: translations.days,
                        value: settings.number_of_days_after_3,
                        group: 1
                    },
                    {
                        id: 'scheduled_to_send_3',
                        name: 'scheduled_to_send_3',
                        label: translations.schedule,
                        type: 'select',
                        options: [
                            {
                                value: consts.reminder_schedule_after_invoice_date,
                                text: translations.after_invoice_date
                            },
                            {
                                value: consts.reminder_schedule_before_due_date,
                                text: translations.before_due_date
                            },
                            {
                                value: consts.reminder_schedule_after_due_date,
                                text: translations.after_due_date
                            }
                        ],
                        value: settings.scheduled_to_send_3
                    },
                    {
                        id: 'amount_to_charge_3',
                        name: 'amount_to_charge_3',
                        label: translations.late_fee_amount,
                        type: 'text',
                        placeholder: translations.late_fee_amount,
                        value: settings.amount_to_charge_3,
                        group: 1
                    },
                    {
                        id: 'percent_to_charge_3',
                        name: 'percent_to_charge_3',
                        label: translations.late_fee_percent,
                        type: 'text',
                        placeholder: translations.late_fee_percent,
                        value: settings.percent_to_charge_3,
                        group: 1
                    },
                    {
                        id: 'reminder3_enabled',
                        name: 'reminder3_enabled',
                        label: translations.send_email,
                        type: 'switch',
                        placeholder: translations.send_email,
                        value: settings.reminder3_enabled,
                        group: 1
                    }
                ]
            }
        }

        return key !== null ? formFields[key] : formFields
    }

    _buildTemplate () {
        const allFields = this.getFormFields(this.props.template_type)
        const test = []

        if (!allFields) {
            return test
        }

        const sectionFields = allFields.fields

        test.push(sectionFields)
        return test
    }

    render () {
        const fields = this.getFormFields()

        const test2 = Object.keys(fields).filter(key2 => {
            if (fields[key2].is_custom || fields[key2].is_reminder || key2 === this.props.template_type) {
                return fields[key2]
            }
        })

        const toMap = this.props.custom_only && this.props.custom_only === true ? test2 : Object.keys(fields)

        const options = toMap.map(key => {
            return <option data-name={fields[key].name} key={key} value={key}>{fields[key].name}</option>
        })

        const test = this._buildTemplate()
        const form = this.props.return_form === true ? <FormBuilder
            handleChange={this.props.handleSettingsChange}
            formFieldsRows={test}
            submitBtnTitle="Calculate"
        /> : null

        return <React.Fragment>
            <FormGroup>
                <Label>{translations.template}</Label>
                <Input type="select"
                    name="template_type"
                    onChange={this.props.handleChange}
                >
                    {options}
                </Input>
            </FormGroup>

            {form}

        </React.Fragment>
    }
}

export default EmailFields
