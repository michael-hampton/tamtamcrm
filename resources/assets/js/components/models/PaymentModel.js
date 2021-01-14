import axios from 'axios'
import moment from 'moment'
import BaseModel from './BaseModel'
import { consts } from '../utils/_consts'

export default class PaymentModel extends BaseModel {
    constructor (invoices, data = null, credits = null) {
        super()

        this.invoices = invoices
        this.credits = credits

        this.errors = []
        this.error_message = ''

        this._url = '/api/payments'
        this.entity = 'Payment'

        this.account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(this.account_id))
        this.settings = user_account[0].account.settings

        this._fields = {
            modal: false,
            deleted_at: null,
            is_deleted: false,
            assigned_to: '',
            customer_id: '',
            company_gateway_id: null,
            account_id: JSON.parse(localStorage.getItem('appState')).user.account_id,
            status_id: null,
            invoice_id: null,
            reference_number: '',
            date: moment(new Date()).format('YYYY-MM-DD'),
            amount: 0,
            refunded: 0,
            applied: 0,
            type_id: this.settings.payment_type_id || '',
            loading: false,
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            private_notes: '',
            errors: [],
            send_email: this.settings.should_send_email_for_manual_payment || false,
            selectedInvoices: [],
            payable_invoices: [],
            payable_credits: [],
            paymentables: [],
            message: ''
        }

        this.completed = consts.payment_status_completed
        this.cancelled = consts.payment_status_voided
        this.failed = consts.payment_status_failed

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }

        if (this.fields.paymentables.length) {
            this.buildPaymentables()
        }
    }

    get fields () {
        return this._fields
    }

    get paymentableCredits () {
        if (!this.credits.length || !this.fields.payable_credits.length) {
            return false
        }

        const creditIds = this.fields.payable_credits.map(paymentable => {
            return parseInt(paymentable.credit_id)
        })

        return this.credits.filter(credit => {
            return creditIds.includes(parseInt(credit.id))
        })
    }

    get paymentableInvoices () {
        if (!this.invoices.length || !this.fields.payable_invoices.length) {
            return false
        }

        const invoiceIds = this.fields.payable_invoices.map(paymentable => {
            return parseInt(paymentable.invoice_id)
        })

        return this.invoices.filter(invoice => {
            return invoiceIds.includes(parseInt(invoice.id))
        })
    }

    get paymentable_invoices () {
        return this.fields.payable_invoices
    }

    get paymentable_credits () {
        return this.fields.payable_credits
    }

    get url () {
        return this._url
    }

    get isDeleted () {
        return this.fields.deleted_at && this.fields.deleted_at.toString().length > 0
    }

    get isCancelled () {
        return this.fields.status_id === this.cancelled
    }

    get isFailed () {
        return this.fields.deleted_at && this.fields.deleted_at.toString().length > 0
    }

    get isArchived () {
        return this.fields.deleted_at && this.fields.deleted_at.toString().length > 0 && this.fields.is_deleted === false
    }

    get isActive () {
        return !this.fields.deleted_at && this.fields.is_deleted === false
    }

    get isCompleted () {
        return this.fields.status_id === this.completed
    }

    get isOnline () {
        return this.fields.company_gateway_id && this.fields.company_gateway_id.toString().length
    }

    get completedAmount () {
        if (this.isDeleted) {
            return 0
        }

        if ([this.cancelled, this.failed].includes(this.fields.status_id)) {
            return 0
        }

        return this.fields.amount - (this.fields.refunded)
    }

    buildPaymentables () {
        if (!this.fields.id || !this.fields.paymentables) {
            return false
        }

        const credits = this.fields.paymentables.filter(paymentable => {
            return paymentable.payment_id === this.fields.id && paymentable.paymentable_type === 'App\\Models\\Credit'
        })

        const invoices = this.fields.paymentables.filter(paymentable => {
            return paymentable.payment_id === this.fields.id && paymentable.paymentable_type === 'App\\Models\\Invoice'
        })

        this.fields.payable_invoices = invoices
        this.fields.payable_credits = credits
    }

    buildDropdownMenu () {
        const actions = []

        if (this.fields.customer_id !== '') {
            actions.push('email')
        }

        if (!this.isDeleted) {
            actions.push('delete')
        }

        if (!this.isDeleted) {
            actions.push('refund')
        }

        if (!this.isDeleted) {
            actions.push('archive')
        }

        if (this.fields.applied < this.fields.amount) {
            actions.push('apply')
        }

        if (this.completedAmount > 0) {
            actions.push('refund')
        }

        return actions
    }

    getInvoice (invoice_id) {
        console.log('all invoices here', this.invoices)

        const invoice = this.invoices.filter(function (invoice) {
            return invoice.id === parseInt(invoice_id)
        })

        if (!invoice.length || !invoice[0]) {
            return false
        }

        return invoice[0]
    }

    getCredit (credit_id) {
        const credit = this.credits.filter(function (credit) {
            return credit.id === parseInt(credit_id)
        })

        if (!credit.length || !credit[0]) {
            return false
        }

        return credit[0]
    }

    getInvoicesByStatus (status) {
        return status ? this.invoices.filter(invoice => invoice.status_id === status) : this.invoices
    }

    getCreditsByStatus (status) {
        return status ? this.credits.filter(credit => credit.status_id === status) : this.credits
    }

    filterInvoicesByCustomer (customer_id) {
        if (customer_id === '') {
            return this.invoices
        }
        return this.invoices.filter(function (invoice) {
            return invoice.customer_id === parseInt(customer_id)
        })
    }

    hasInvoice (items) {
        const filtered = items.filter((item) => {
            return item.amount !== null && item.amount !== 0 && !isNaN(item.amount) && item.amount.toString().length
        })

        return filtered.length > 0
    }

    filterCreditsByCustomer (customer_id) {
        if (customer_id === '') {
            return this.credits
        }
        return this.credits.filter(function (credit) {
            return credit.customer_id === parseInt(customer_id)
        })
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

    async save (data) {
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

    calculateRefundedAmount (paymentables) {
        let refunded = 0

        console.log('paymentables', paymentables)

        paymentables.map((paymentable, idx) => {
            if (paymentable.refunded > 0) {
                refunded += parseFloat(paymentable.refunded)
            }
        })

        return refunded
    }
}
