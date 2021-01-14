import axios from 'axios'
import moment from 'moment'
import BaseModel, { LineItem } from './BaseModel'
import { consts } from '../utils/_consts'

export const quote_pdf_fields = ['$quote.number', '$quote.po_number', '$quote.quote_date', '$quote.valid_until', '$quote.balance_due', '$quote.quote_datetime', '$quote.quote_agent',
    '$quote.quote_total', '$quote.partial_due', '$quote.custom1', '$quote.custom2', '$quote.custom3', '$quote.custom4', '$quote.surcharge1',
    '$quote.surcharge2', '$invoice.surcharge3', '$invoice.surcharge4'
]

export default class QuoteModel extends BaseModel {
    constructor (data = null, customers = []) {
        super()
        this.customers = customers
        this._url = '/api/quote'
        this.entity = 'Quote'
        this.errors = []
        this.error_message = ''

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }

        this._fields = {
            is_mobile: window.innerWidth <= 768,
            modalOpen: false,
            is_amount_discount: false,
            invitations: [],
            customer_id: '',
            project_id: '',
            invoice_id: '',
            assigned_to: '',
            number: '',
            user_id: null,
            contacts: [],
            due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            quantity: '',
            id: null,
            account_id: JSON.parse(localStorage.getItem('appState')).user.account_id,
            lines: [],
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
            line_items: [],
            date: moment(new Date()).format('YYYY-MM-DD'),
            partial: 0,
            has_partial: false,
            partial_due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
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
            discount: 0,
            total_custom_values: 0,
            total_custom_tax: 0,
            recurring: '',
            recurring_quote_id: '',
            activeTab: '1',
            po_number: '',
            design_id: '',
            currency_id: this.settings.currency_id.toString().length ? this.settings.currency_id : consts.default_currency,
            exchange_rate: 1,
            success: false,
            showSuccessMessage: false,
            showErrorMessage: false,
            loading: false,
            changesMade: false
        }

        this.sent = consts.quote_status_sent
        this.approved = consts.quote_status_approved

        this.customer = null

        if (data !== null) {
            this._fields = { ...this.fields, ...data }

            this.updateCustomer()
        }

        if (this.customer && this.customer.currency_id.toString().length) {
            const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === this.customer.currency_id)
            this.exchange_rate = currency[0].exchange_rate
        }

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.account = user_account[0]
    }

    get id () {
        return this.fields.id
    }

    get exchange_rate () {
        return this.fields.exchange_rate
    }

    set exchange_rate (exchange_rate) {
        this.fields.exchange_rate = exchange_rate
    }

    get isNew () {
        return !this.fields.id || !this.fields.id.toString().length || parseInt(this.fields.id) <= 0
    }

    get customer () {
        return this._customer
    }

    set customer (customer) {
        this._customer = customer
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get isApproved () {
        return parseInt(this.fields.status_id) === this.approved
    }

    get isSent () {
        return parseInt(this.fields.status_id) === this.sent
    }

    get isDraft () {
        return parseInt(this.fields.status_id) === consts.quote_status_draft
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
        return `http://${this.account.account.subdomain}portal/quotes/$key`
    }

    get getInvitationViewLink () {
        return !this.invitations || !this.invitations.length ? '' : `http://${this.account.account.subdomain}portal/view/quote/${this.invitations[0].key}`
    }

    get customer_id () {
        return this.fields.customer_id
    }

    set customer_id (customer_id) {
        this.fields.customer_id = customer_id
        this.updateCustomer()
    }

    get hasInvoice () {
        return this.fields.invoice_id.toString().length
    }

    get contacts () {
        const index = this.customers.findIndex(customer => customer.id === this.fields.customer_id)
        const customer = this.customers[index]
        return customer.contacts ? customer.contacts : []
    }

    updateCustomer () {
        if (this.customers.length && this._fields.customer_id) {
            const customer = this.customers.filter(customer => customer.id === parseInt(this._fields.customer_id))
            this.customer = customer[0]
        }
    }

    buildDropdownMenu () {
        const actions = []

        if (this.fields.invitations.length) {
            actions.push('pdf')
        }

        if (this.fields.customer_id !== '') {
            actions.push('email')
        }

        if (!this.isSent) {
            actions.push('markSent')
        }

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
        }

        if (this.fields.task_id && this.fields.task_id !== '') {
            actions.push('getProducts')
        }

        if (this.isSent) {
            actions.push('approve')
            actions.push('reject')
        }

        if (!this.fields.deleted_at && !this.isDraft) {
            actions.push('portal')
        }

        actions.push('cloneToQuote')

        if (this.isModuleEnabled('orders')) {
            actions.push('clone_to_order')
        }

        if (this.isModuleEnabled('credits')) {
            actions.push('cloneToCredit')
        }

        if (this.isModuleEnabled('invoices')) {
            actions.push('cloneQuoteToInvoice')
        }

        if (!this.fields.recurring_quote_id && this.isModuleEnabled('recurringQuotes')) {
            actions.push('cloneToRecurringQuote')
        }

        return actions
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

    addItem () {
        const newArray = this.fields.line_items.slice()
        newArray.push(LineItem)
        this.fields.line_items = newArray
        return newArray
    }

    removeItem (index) {
        const array = [...this.fields.line_items] // make a separate copy of the array
        array.splice(index, 1)
        this.fields.line_items = array
        return array
    }

    isLate () {
        const dueDate = moment(this._fields.due_date).format('YYYY-MM-DD')
        const pending_statuses = [consts.quote_status_draft, consts.quote_status_sent]

        return moment().isAfter(dueDate) && pending_statuses.includes(this._fields.status_id)
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

    customerChange (customer_id) {
        const index = this.customers.findIndex(customer => customer.id === parseInt(customer_id))

        const customer = this.customers[index]
        // const address = customer.billing ? {
        //     line1: customer.billing.address_1,
        //     town: customer.billing.address_2,
        //     county: customer.billing.city,
        //     country: 'United Kingdom'
        // } : null

        const contacts = customer && customer.contacts ? customer.contacts : []

        return {
            customer: customer,
            customerName: customer.name,
            contacts: contacts
            // address: address

        }
    }
}
