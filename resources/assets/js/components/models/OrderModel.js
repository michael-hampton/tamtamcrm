import axios from 'axios'
import moment from 'moment'
import BaseModel, { LineItem } from './BaseModel'
import { consts } from '../utils/_consts'

export const order_pdf_fields = ['$order.number', '$order.po_number', '$order.order_date', '$order.order_total', '$order.order_datetime', '$order.order_agent',
    '$order.balance', '$order.partial_due', '$order.custom1', '$order.custom2', '$order.custom3', '$order.custom4',
    '$order.surcharge1', '$order.surcharge2', '$order.surcharge3', '$order.surcharge4'
]

export default class OrderModel extends BaseModel {
    constructor (data = null, customers = []) {
        super()
        this._url = '/api/order'
        this.customers = customers
        this.entity = 'Order'

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }

        this._fields = {
            is_mobile: window.innerWidth <= 768,
            modalOpen: false,
            deleted_at: null,
            assigned_to: '',
            number: '',
            is_amount_discount: false,
            id: null,
            account_id: JSON.parse(localStorage.getItem('appState')).user.account_id,
            showSuccessMessage: false,
            showErrorMessage: false,
            invitations: [],
            contacts: [],
            address: {},
            customer_id: '',
            invoice_id: null,
            project_id: '',
            total: 0,
            design_id: '',
            date: moment(new Date()).format('YYYY-MM-DD'),
            due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            custom_value1: '',
            public_notes: '',
            private_notes: '',
            footer: '',
            terms: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            transaction_fee_tax: false,
            shipping_cost_tax: false,
            transaction_fee: 0,
            shipping_cost: 0,
            gateway_fee: 0,
            gateway_percentage: false,
            tax_rate_name: '',
            tax_rate: 0,
            tax_rate_name_2: '',
            tax_rate_name_3: '',
            tax_2: 0,
            tax_3: 0,
            // company_id: this.props.add === false && this.props.invoice && this.props.invoice.company_id ? this.props.invoice.company_id : '',
            status_id: null,
            tasks: [],
            errors: [],
            discount_total: 0,
            tax: 0,
            discount: 0,
            tax_total: 0,
            sub_total: 0,
            line_items: [],
            partial: 0,
            has_partial: false,
            partial_due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            activeTab: '1',
            po_number: '',
            currency_id: this.settings.currency_id.toString().length ? this.settings.currency_id : consts.default_currency,
            exchange_rate: 1,
            loading: false,
            dropdownOpen: false,
            changesMade: false,
            message: '',
            success: false
        }

        this.sent = consts.order_status_sent
        this.approved = consts.order_status_approved
        this.completed = consts.order_status_complete
        this.held = consts.order_status_held
        this.backorder = consts.order_status_backorder
        this.cancelled = consts.order_status_cancelled

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

    get exchange_rate () {
        return this.fields.exchange_rate
    }

    set exchange_rate (exchange_rate) {
        this.fields.exchange_rate = exchange_rate
    }

    get id () {
        return this.fields.id
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

    get isSent () {
        return parseInt(this.fields.status_id) === consts.order_status_sent
    }

    get isApproved () {
        return parseInt(this.fields.status_id) === consts.order_status_approved
    }

    get isCompleted () {
        return parseInt(this.fields.status_id) === consts.order_status_complete
    }

    get isCancelled () {
        return parseInt(this.fields.status_id) === consts.order_status_cancelled
    }

    get isBackorder () {
        return parseInt(this.fields.status_id) === consts.order_status_backorder
    }

    get isHeld () {
        return parseInt(this.fields.status_id) === consts.order_status_held
    }

    get isDraft () {
        return parseInt(this.fields.status_id) === consts.order_status_draft
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
        return `http://${this.account.account.subdomain}portal/orders/$key`
    }

    get getInvitationViewLink () {
        return !this.invitations || !this.invitations.length ? '' : `http://${this.account.account.subdomain}portal/view/order/${this.invitations[0].key}`
    }

    get customer_id () {
        return this.fields.customer_id
    }

    set customer_id (customer_id) {
        this.fields.customer_id = customer_id
        this.updateCustomer()
    }

    get isDeleted () {
        return this.fields.deleted_at && this.fields.deleted_at.length > 0
    }

    get isEditable () {
        return !this.isCancelled && !this.isHeld && !this.isDeleted
    }

    get contacts () {
        const index = this.customers.findIndex(customer => customer.id === this.fields.customer_id)
        const customer = this.customers[index]
        return customer.contacts ? customer.contacts : []
    }

    set task_id (task_id) {
        this._fields.task_id = task_id
    }

    updateCustomer () {
        if (this.customers.length && this._fields.customer_id) {
            const customer = this.customers.filter(customer => customer.id === parseInt(this._fields.customer_id))
            this.customer = customer[0]
        }
    }

    hasInvoice () {
        return this.fields.invoice_id && this.fields.invoice_id.length
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
        const dueDate = moment(this._fields.due_date).format('YYYY-MM-DD HH::MM:SS')
        const pending_statuses = [consts.order_status_draft, consts.order_status_backorder, consts.order_status_held, consts.order_status_partial]

        return moment().isAfter(dueDate) && pending_statuses.includes(this._fields.status_id)
    }

    buildDropdownMenu () {
        const actions = []

        if (this.fields.invitations.length) {
            actions.push('pdf')
        }

        if (this.fields.customer_id !== '') {
            actions.push('email')
        }

        if (!this.isSent && this.isEditable) {
            actions.push('markSent')
            actions.push('dispatch_note')
        }

        if (!this.isCancelled) {
            actions.push('cancel')
        }

        if (!this.isApproved && !this.isCompleted && this.isEditable) {
            actions.push('dispatch')
        }

        if (this.isBackorder && this.isEditable) {
            actions.push('fulfill')
        }

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
        }

        if (this.isModuleEnabled('invoices') && this.isEditable) {
            actions.push('cloneOrderToInvoice')
        }

        if (this.isModuleEnabled('invoices') && !this.isApproved && this.isEditable) {
            actions.push('convert')
        }

        if (this.isModuleEnabled('quotes') && this.isEditable) {
            actions.push('cloneOrderToQuote')
        }

        if (!this.hasInvoice() && !this.isCompleted && this.isEditable) {
            actions.push('holdOrder')
        }

        if (!this.fields.deleted_at && !this.isDraft) {
            actions.push('portal')
        }

        if (this.isHeld) {
            actions.push('reverse_status')
        }

        return actions
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
}
