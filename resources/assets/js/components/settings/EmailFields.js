import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import FormBuilder from './FormBuilder'
import { translations } from '../utils/_translations'
import { frequencyOptions } from '../utils/_consts'

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
        const templates = this.props.templates
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
            invoice: {
                name: translations.invoice,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.invoice.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.invoice.message,
                        group: 1
                    }
                ]
            },

            payment: {
                name: translations.payment,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.payment.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.payment.message,
                        group: 1
                    }
                ]
            },
            statement: {
                name: 'Statement',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.statement.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.statement.message,
                        group: 1
                    }
                ]
            },
            payment_partial: {
                name: 'Partial Payment',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.payment_partial.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.payment_partial.message,
                        group: 1
                    }
                ]
            },
            quote: {
                name: translations.quote,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.quote.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.quote.message,
                        group: 1
                    }
                ]
            },
            credit: {
                name: translations.credit,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.credit.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.credit.message,
                        group: 1
                    }
                ]
            },
            lead: {
                name: translations.lead,
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.lead.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.lead.message,
                        group: 1
                    }
                ]
            },
            deal: {
                name: translations.deal,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.deal.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.deal.message,
                        group: 1
                    }
                ]
            },
            task: {
                name: translations.task,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.task.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'email_template_task',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.task.message,
                        group: 1
                    }
                ]
            },
            case: {
                name: translations.cases,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.case.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.case.message,
                        group: 1
                    }
                ]
            },
            purchase_order: {
                name: translations.purchase_order,
                is_remider: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.purchase_order.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.purchase_order.message,
                        group: 1
                    }
                ]
            },
            order_received: {
                name: 'Order Received',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.order_received.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.order_received.message,
                        group: 1
                    }
                ]
            },
            order_sent: {
                name: 'Order Sent',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.order_sent.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.order_sent.message,
                        group: 1
                    }
                ]
            },
            endless: {
                name: 'Endless',
                is_reminder: false,
                is_custom: false,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.endless.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.endless.message,
                        group: 1
                    },
                    {
                        id: 'endless_reminder_frequency_id',
                        name: 'endless_reminder_frequency_id',
                        label: translations.schedule,
                        type: 'select',
                        options: frequencies,
                        value: templates.endless.frequency_id
                    },
                    {
                        id: 'amount_to_charge_endless',
                        name: 'amount_to_charge_endless',
                        label: translations.late_fee_amount,
                        type: 'text',
                        placeholder: translations.late_fee_amount,
                        value: templates.endless.amount_to_charge,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'percent_to_charge_endless',
                        label: 'Fee Percent',
                        type: 'text',
                        placeholder: 'Fee Percent',
                        value: templates.endless.percent_to_charge,
                        group: 1
                    }
                ]
            },
            custom1: {
                name: 'Custom 1',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.custom1.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.custom1.message,
                        group: 1
                    }
                ]
            },
            custom2: {
                name: 'Custom 2',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.custom2.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.custom2.message,
                        group: 1
                    }
                ]
            },
            custom3: {
                name: 'Custom 3',
                is_reminder: false,
                is_custom: true,
                fields: [
                    {
                        id: 'subject',
                        name: 'subject',
                        label: translations.subject,
                        type: 'text',
                        placeholder: translations.subject,
                        value: templates.custom3.subject,
                        group: 1
                    },
                    {
                        id: 'body',
                        name: 'message',
                        label: translations.body,
                        type: 'textarea',
                        inputClass: 'textarea-lg',
                        placeholder: translations.body,
                        value: templates.custom3.message,
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
            return <option data-name={key} key={key} value={key}>{fields[key].name}</option>
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
                    value={this.props.selected_template || this.props.template_type}
                    name="template_type"
                    onChange={this.props.handleChange}
                >
                    <option value="">{translations.select_option}</option>
                    {options}
                </Input>
            </FormGroup>

            {form}

        </React.Fragment>
    }
}

export default EmailFields
