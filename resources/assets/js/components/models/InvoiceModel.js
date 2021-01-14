import axios from 'axios'
import moment from 'moment'
import BaseModel, { LineItem } from './BaseModel'
import { consts } from '../utils/_consts'

export const invoice_pdf_fields = ['$invoice.number', '$invoice.po_number', '$invoice.invoice_date', '$invoice.invoice_datetime', '$invoice.invoice_agent', '$invoice.due_date',
    '$invoice.balance', '$invoice.invoice_total', '$invoice.partial_due', '$invoice.custom1', '$invoice.custom2', '$invoice.custom3',
    '$invoice.custom4', '$invoice.surcharge1', '$invoice.surcharge2', '$invoice.surcharge3', '$invoice.surcharge4'
]

export default class InvoiceModel extends BaseModel {
    constructor (data = null, customers = []) {
        super()
        this.customers = customers
        this._url = '/api/invoice'
        this.entity = 'Invoice'

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }

        this._fields = {
            line_type: null,
            project_id: null,
            is_mobile: window.innerWidth <= 768,
            modalOpen: false,
            is_amount_discount: false,
            deleted_at: null,
            assigned_to: '',
            number: '',
            invitations: [],
            emails: [],
            customer_id: '',
            user_id: null,
            contacts: [],
            due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            quantity: '',
            id: null,
            account_id: JSON.parse(localStorage.getItem('appState')).user.account_id,
            line_items: [],
            address: {},
            customerName: '',
            tax_rate_name: '',
            tax_rate: 0,
            tax_rate_name_2: '',
            tax_rate_name_3: '',
            tax_2: 0,
            tax_3: 0,
            company_id: '',
            status_id: null,
            tasks: [],
            errors: [],
            total: 0,
            discount_total: 0,
            tax_total: 0,
            sub_total: 0,
            data: [],
            date: moment(new Date()).format('YYYY-MM-DD'),
            partial: 0,
            partial_due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            has_partial: false,
            public_notes: '',
            private_notes: '',
            terms: '',
            footer: '',
            visible: 'collapse',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            transaction_fee_tax: false,
            shipping_cost_tax: false,
            transaction_fee: 0,
            shipping_cost: 0,
            gateway_fee: 0,
            gateway_percentage: false,
            tax: 0,
            total_custom_values: 0,
            total_custom_tax: 0,
            discount: 0,
            recurring: '',
            activeTab: '1',
            po_number: '',
            design_id: '',
            recurring_invoice_id: null,
            currency_id: this.settings.currency_id.toString().length ? this.settings.currency_id : consts.default_currency,
            exchange_rate: 1,
            success: false,
            showSuccessMessage: false,
            showErrorMessage: false,
            loading: false,
            changesMade: false,
            viewed: false
        }

        this.approved = 4
        this.reversed = consts.invoice_status_reversed
        this.cancelled = consts.invoice_status_cancelled
        this.paid = consts.invoice_status_paid
        this.sent = consts.invoice_status_sent
        this.partial = consts.invoice_status_partial

        this.customer = null

        if (data !== null) {
            this._fields = { ...this.fields, ...data }

            this.updateCustomer()
        }

        this.exchange_rate = this.currency ? this.currency.exchange_rate : 1

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.account = user_account[0]
    }

    get currency () {
        const currency_id = this.customer.length && this.customer.currency_id.toString().length ? this.customer.currency_id : this.settings.currency_id

        if (!currency_id) {
            return null
        }

        return JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(currency_id))[0]
    }

    get isNew () {
        return !this.fields.id || !this.fields.id.toString().length || parseInt(this.fields.id) <= 0
    }

    get fields () {
        return this._fields
    }

    get exchange_rate () {
        return this.fields.exchange_rate
    }

    set exchange_rate (exchange_rate) {
        this.fields.exchange_rate = exchange_rate
    }

    get customer () {
        return this._customer || []
    }

    set customer (customer) {
        this._customer = customer
    }

    get isViewed () {
        return parseInt(this.fields.status_id) === this.sent && this.fields.viewed === true
    }

    get isApproved () {
        return parseInt(this.fields.status_id) === this.approved
    }

    get isReversed () {
        return parseInt(this.fields.status_id) === this.reversed
    }

    get isCancelled () {
        return parseInt(this.fields.status_id) === this.cancelled
    }

    get isPaid () {
        return parseInt(this.fields.status_id) === this.paid
    }

    get isDraft () {
        return parseInt(this.fields.status_id) === consts.invoice_status_draft
    }

    get isSent () {
        return parseInt(this.fields.status_id) === this.sent
    }

    get isPartial () {
        return parseInt(this.fields.status_id) === this.partial
    }

    get isDeleted () {
        return this.fields.deleted_at && this.fields.deleted_at.length > 0
    }

    get isEditable () {
        return !this.isReversed && !this.isCancelled && !this.isDeleted
    }

    get id () {
        return this.fields.id
    }

    get fileCount () {
        return this._file_count || 0
    }

    set fileCount (files) {
        this._file_count = files ? files.length : 0
    }

    get invitations () {
        return this.fields.invitations
    }

    get invitation_link () {
        return `http://${this.account.account.subdomain}portal/invoices/$key`
    }

    get getInvitationViewLink () {
        return !this.invitations || !this.invitations.length ? '' : `http://${this.account.account.subdomain}portal/view/invoice/${this.invitations[0].key}`
    }

    get customer_id () {
        return this.fields.customer_id
    }

    set customer_id (customer_id) {
        this.fields.customer_id = customer_id
        this.updateCustomer()
    }

    get contacts () {
        const index = this.customers.findIndex(customer => customer.id === this.fields.customer_id)
        const customer = this.customers[index]
        return customer.contacts ? customer.contacts : []
    }

    get url () {
        return this._url
    }

    updateCustomer () {
        if (this.customers.length && this._fields.customer_id) {
            const customer = this.customers.filter(customer => customer.id === parseInt(this._fields.customer_id))
            this.customer = customer[0]
        }
    }

    buildInvitations (contact, add = false) {
        const invitations = this.fields.invitations

        // check if the check box is checked or unchecked
        if (add) {
            // add the numerical value of the checkbox to options array
            invitations.push({ contact_id: contact })
        } else {
            // or remove the value from the unchecked checkbox from the array
            const index = invitations.findIndex(contact => contact.contact_id === contact)
            invitations.splice(index, 1)
        }

        return invitations
    }

    buildDropdownMenu () {
        const actions = []

        if (this.fields.invitations.length) {
            actions.push('pdf')
        }

        actions.push('dispatch_note')

        if (this.fields.customer_id !== '') {
            actions.push('email')
        }

        if (!this.isPaid) {
            actions.push('newPayment')
        }

        if (!this.isSent && this.isEditable) {
            actions.push('markSent')
        }

        if (this.isCancelled || this.isReversed) {
            actions.push('reverse_status')
        }

        if ((this.isSent || this.isPartial) && !this.isPaid && this.isEditable) {
            actions.push('markPaid')
        }

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
        }

        if (!this.fields.deleted_at && this.isSent && !this.isCancelled) {
            actions.push('cancel')
        }

        if (!this.fields.deleted_at && !this.isDraft && !this.isCancelled) {
            actions.push('portal')
        }

        if (!this.fields.deleted_at && (this.isSent || this.isPaid) && !this.isReversed) {
            actions.push('reverse')
        }

        if (this.fields.task_id && this.fields.task_id !== '' && this.isEditable) {
            actions.push('getProducts')
        }

        if (this.isEditable) {
            actions.push('cloneToInvoice')
        }

        if (this.isModuleEnabled('quotes') && this.isEditable) {
            actions.push('cloneInvoiceToQuote')
        }

        if (this.isModuleEnabled('credits') && this.isEditable) {
            actions.push('cloneToCredit')
        }

        if (!this.fields.recurring_invoice_id && this.isModuleEnabled('recurringInvoices')) {
            actions.push('cloneToRecurringInvoice')
        }

        return actions
    }

    addItem () {
        // const newArray = this.fields.line_items.slice()
        this.fields.line_items.push(LineItem)
        return this.fields.line_items
    }

    removeItem (index) {
        const array = [...this.fields.line_items] // make a separate copy of the array
        array.splice(index, 1)
        this.fields.line_items = array
        return array
    }

    async completeAction (data, action) {
        if (!this.fields.id) {
            return false
        }

        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(`${this.url}/${this.fields.id}/${action}`, data)

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }
            // Don't forget to return something
            return res.data
        } catch (e) {
            this.handleError(e)
            return false
        }
    }

    async update (data) {
        if (!this.fields.id) {
            return false
        }

        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.put(`${this.url}/${this.fields.id}`, data)

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }
            // Don't forget to return something
            return res.data
        } catch (e) {
            this.handleError(e)
            return false
        }
    }

    isLate () {
        const dueDate = moment(this._fields.due_date).format('YYYY-MM-DD HH::MM:SS')
        const pending_statuses = [consts.invoice_status_draft, consts.invoice_status_sent, consts.invoice_status_partial]

        return moment().isAfter(dueDate) && pending_statuses.includes(this._fields.status_id)
    }

    async save (data) {
        if (this.fields.id) {
            return this.update(data)
        }

        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post(this.url, data)

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }
            // Don't forget to return something
            return res.data
        } catch (e) {
            this.handleError(e)
            return false
        }
    }

    async loadPdf () {
        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post('api/preview', { entity: this.entity, entity_id: this._fields.id })

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }

            // Don't forget to return something
            return this.buildPdf(res.data)
        } catch (e) {
            alert(e)
            this.handleError(e)
            return false
        }
    }

    customerChange (customer_id) {
        const index = this.customers.findIndex(customer => customer.id === parseInt(customer_id))
        const customer = this.customers[index]
        const address = customer.billing ? {
            line1: customer.billing.address_1,
            town: customer.billing.address_2,
            county: customer.billing.city,
            country: 'United Kingdom'
        } : null

        const contacts = customer.contacts ? customer.contacts : []

        return {
            customer: customer,
            customerName: customer.name,
            contacts: contacts,
            address: address

        }
    }

    calculateTaxes (usesInclusiveTaxes) {
        let tax_total = 0

        if (this.fields.tax_rate > 0) {
            const a_total = parseFloat(this.fields.total)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_rate) / 100
            tax_total += tax_percentage
        }

        if (this.fields.tax_2 && this.fields.tax_2 > 0) {
            const a_total = parseFloat(this.fields.total)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_2) / 100
            tax_total += tax_percentage
        }

        if (this.fields.tax_3 && this.fields.tax_3 > 0) {
            const a_total = parseFloat(this.fields.total)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_3) / 100
            tax_total += tax_percentage
        }

        this.fields.line_items.map((product) => {
            const quantity = product.quantity === 0 ? 1 : product.quantity
            let line_total = product.unit_price * quantity
            let discount_total = 0

            if (product.unit_discount > 0 && this.fields.discount === 0) {
                const n = parseFloat(this.fields.total)

                if (this.fields.is_amount_discount === true) {
                    discount_total += parseFloat(product.unit_discount)
                } else {
                    const percentage = n * product.unit_discount / 100
                    discount_total += percentage
                    // lexieTotal -= discount_total
                }

                line_total -= discount_total
            }

            if (product.unit_tax > 0) {
                const tax_percentage = line_total * product.unit_tax / 100
                tax_total += tax_percentage
            }
        })

        const precision = this.currency.precision || 2

        return Math.round(tax_total, precision)
    }

    calculateTax (tax_amount) {
        const a_total = parseFloat(this.fields.total)
        const tax_percentage = parseFloat(a_total) * parseFloat(tax_amount) / 100

        const precision = this.currency.precision || 2

        return Math.round(tax_percentage, precision)
    }
}
