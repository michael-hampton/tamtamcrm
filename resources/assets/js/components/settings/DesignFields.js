import { translations } from '../utils/_translations'

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

export default function DesignFields (settings) {
    const fields = []

    const modules = JSON.parse(localStorage.getItem('modules'))

    if (modules && modules.invoices) {
        fields.push({
            name: 'invoice_design_id',
            label: 'Invoice Design',
            type: 'select',
            value: settings.invoice_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.tasks) {
        fields.push({
            name: 'task_design_id',
            label: 'Task Design',
            type: 'select',
            value: settings.task_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.quotes) {
        fields.push({
            name: 'quote_design_id',
            label: translations.quote_design,
            type: 'select',
            value: settings.quote_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.deals) {
        fields.push({
            name: 'deal_design_id',
            label: 'Deal Design',
            type: 'select',
            value: settings.deal_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.credits) {
        fields.push({
            name: 'credit_design_id',
            label: translations.credit_design,
            type: 'select',
            value: settings.credit_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.leads) {
        fields.push({
            name: 'lead_design_id',
            label: 'Lead Design',
            type: 'select',
            value: settings.lead_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.cases) {
        fields.push({
            name: 'case_design_id',
            label: 'Case Design',
            type: 'select',
            value: settings.case_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.purchase_orders) {
        fields.push({
            name: 'purchase_order_design_id',
            label: 'Purchase Order Design',
            type: 'select',
            value: settings.purchase_order_design_id,
            options: design_options,
            group: 1
        })
    }

    if (modules && modules.orders) {
        fields.push({
            name: 'order_design_id',
            label: 'Order Design',
            type: 'select',
            value: settings.order_design_id,
            options: design_options,
            group: 1
        })
    }

    return fields
}
