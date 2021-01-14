import axios from 'axios'
import moment from 'moment'
import BaseModel from './BaseModel'
import { consts } from '../utils/_consts'

export default class ExpenseModel extends BaseModel {
    constructor (data = null, customers = null) {
        super()
        this.customers = customers
        this.errors = []
        this.error_message = ''
        this.currencies = JSON.parse(localStorage.getItem('currencies'))
        this._url = '/api/expense'
        this.entity = 'Expense'

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }

        this._fields = {
            modal: false,
            number: '',
            amount: 0,
            amount_before_tax: 0,
            assigned_to: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            public_notes: '',
            private_notes: '',
            tax_rate: 0,
            tax_2: 0,
            tax_3: 0,
            tax_amount1: data !== null && this.settings.expense_taxes_calculated_by_amount === true && data.tax_rate ? data.tax_rate : 0,
            tax_amount2: data !== null && this.settings.expense_taxes_calculated_by_amount === true && data.tax_2 ? data.tax_2 : 0,
            tax_amount3: data !== null && this.settings.expense_taxes_calculated_by_amount === true && data.tax_3 ? data.tax_3 : 0,
            tax_total: 0,
            tax_rate_name_2: '',
            tax_rate_name_3: '',
            tax_rate_name: '',
            project_id: null,
            customer_id: '',
            currency_id: this.settings.currency_id.toString().length ? this.settings.currency_id : consts.default_currency,
            invoice_currency_id: this.settings.currency_id.toString().length ? this.settings.currency_id : consts.default_currency,
            payment_type_id: '',
            exchange_rate: 1,
            reference_number: '',
            payment_date: this.settings.create_expense_payment ? moment(new Date()).add(1, 'days').format('YYYY-MM-DD') : '',
            include_documents: this.settings.include_expense_documents || false,
            create_invoice: this.settings.create_expense_invoice || false,
            date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            company_id: '',
            expense_category_id: '',
            user_id: null,
            notes: '',
            loading: false,
            errors: [],
            message: '',
            activeTab: '1',
            currencyOpen: this.settings.convert_expense_currency || false,
            paymentOpen: this.settings.create_expense_payment || false,
            changesMade: false,
            dropdownOpen: false,
            showSuccessMessage: false,
            showErrorMessage: false,
            is_recurring: false,
            recurring_start_date: '',
            recurring_end_date: '',
            recurring_due_date: '',
            last_sent_date: '',
            next_send_date: '',
            recurring_frequency: 0,
            amount_with_tax: 0,
            expenses_have_inclusive_taxes: this.settings.expenses_have_inclusive_taxes || false,
            expense_taxes_calculated_by_amount: this.settings.expense_taxes_calculated_by_amount || false
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
            console.log('customer', this.customer)
            this.fields.currency_id = this.customer.length && this.customer.currency_id ? this.customer.currency_id : this.settings.currency_id
        }
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get convertedAmount () {
        const gross = parseFloat(this.grossAmount)
        return gross * this.exchange_rate
    }

    get convertedNetAmount () {
        const net = parseFloat(this.netAmount)
        return net * this.exchange_rate
    }

    get id () {
        return this.fields.id
    }

    get exchange_rate () {
        return !this.fields.exchange_rate ? 1 : this.fields.exchange_rate
    }

    set customer_id (customer_id) {
        this.fields.customer_id = customer_id
    }

    get netAmount () {
        return this.fields.expenses_have_inclusive_taxes ? this.fields.amount - this.amountWithTax : this.fields.amount
    }

    get amount () {
        return parseFloat(this.fields.amount)
    }

    set amount (amount) {
        this.fields.amount = parseFloat(amount)
    }

    get grossAmount () {
        return this.fields.expenses_have_inclusive_taxes === true ? this.amount : this.amount + this.amountWithTax
    }

    get calculateTaxRate1 () {
        if (this.fields.expense_taxes_calculated_by_amount === true) {
            if (this.fields.expenses_have_inclusive_taxes === true) {
                return this.fields.tax_amount1 / (this.amount - this.fields.tax_amount1) * 100
            } else {
                return this.fields.tax_amount1 / this.amount * 100
            }
        } else {
            return this.fields.tax_amount1
        }
    }

    get calculateTaxRate2 () {
        return this.fields.expense_taxes_calculated_by_amount === true
            ? this.fields.tax_amount2 / (this.amount - this.fields.tax_amount2) * 100
            : this.fields.tax_2
    }

    get calculateTaxRate3 () {
        return this.fields.expense_taxes_calculated_by_amount === true
            ? this.fields.tax_amount3 / (this.amount - this.fields.tax_amount3) * 100
            : this.fields.tax_3
    }

    get amountWithTax () {
        let total = 0.0

        if (this.fields.expense_taxes_calculated_by_amount === true) {
            total += this.fields.tax_amount1 + this.fields.tax_amount2 + this.fields.tax_amount3
            return total
        }

        if (this.fields.expenses_have_inclusive_taxes === true) {
            if (this.fields.tax_rate > 0) {
                total += this.amount - (this.amount / (1 + (this.fields.tax_rate / 100)))
            }
            if (this.fields.tax_2 > 0) {
                total += this.amount - (this.amount / (1 + (this.fields.tax_2 / 100)))
            }
            if (this.fields.tax_3 > 0) {
                total += this.amount - (this.amount / (1 + (this.fields.tax_3 / 100)))
            }

            return total
        }

        if (this.fields.tax_rate > 0) {
            total += this.amount * this.fields.tax_rate / 100
        }
        if (this.fields.tax_2 > 0) {
            total += this.amount * this.fields.tax_2 / 100
        }
        if (this.fields.tax_3 > 0) {
            total += this.amount * this.fields.tax_3 / 100
        }

        return total
    }

    get convertedAmountWithTax () {
        return Math.round((this.grossAmount * this.exchange_rate), 2)
    }

    get currencyId () {
        if (!this.fields.currency_id) {
            return null
        }

        return parseInt(this.fields.currency_id)
    }

    get fileCount () {
        return this._file_count || 0
    }

    set fileCount (files) {
        this._file_count = files ? files.length : 0
    }

    get company () {
        if (!this.fields.company_id.toString().length) {
            return null
        }

        // TODO
        return null
    }

    get customer () {
        return this.customers &&
        this.customers.length &&
        this.fields.customer_id &&
        this.fields.customer_id.toString().length
            ? this.customers.filter(customer => customer.id === parseInt(this.fields.customer_id))[0]
            : []
    }

    get isConverted () {
        return parseInt(this.fields.exchange_rate) !== 1 && parseInt(this.fields.exchange_rate) !== 0
    }

    getExchangeRateForCurrency (currency_id) {
        const currency = this.currencies && this.currencies.length ? this.currencies.filter(currency => currency.id === parseInt(currency_id)) : []

        console.log('currency', currency)

        return currency.length && currency[0].exchange_rate && currency[0].exchange_rate > 0 ? currency[0].exchange_rate : 1
    }

    buildDropdownMenu () {
        const actions = []
        if (!this.fields.is_deleted) {
            actions.push('newInvoice')
        }

        actions.push('cloneExpense')

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

    setTaxValues (entity) {
        console.log('entity', entity)
        if (entity.tax_rate > 0) {
            this.fields.tax_rate = parseFloat(entity.tax_rate)
        }

        if (entity.tax_2 && entity.tax_2 > 0) {
            this.fields.tax_2 = parseFloat(entity.tax_2)
        }

        if (entity.tax_3 && entity.tax_3 > 0) {
            this.fields.tax_3 = parseFloat(entity.tax_3)
        }

        if (entity.tax_amount1 && entity.tax_amount1 > 0) {
            this.fields.tax_amount1 = parseFloat(entity.tax_amount1)
        }

        if (entity.tax_amount2 && entity.tax_amount2 > 0) {
            this.fields.tax_amount2 = parseFloat(entity.tax_amount2)
        }

        if (entity.tax_amount3 && entity.tax_amount3 > 0) {
            this.fields.tax_amount3 = parseFloat(entity.tax_amount3)
        }
    }

    /* calculateTotals (entity) {
        let tax_total = 0

        if (entity.tax_rate > 0) {
            this.fields.tax_rate = entity.tax_rate
            const a_total = parseFloat(entity.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(entity.tax_rate) / 100
            tax_total += tax_percentage
        }

        if (entity.tax_2 && entity.tax_2 > 0) {
            this.fields.tax_2 = entity.tax_2
            const a_total = parseFloat(entity.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(entity.tax_2) / 100
            tax_total += tax_percentage
        }

        if (entity.tax_3 && entity.tax_3 > 0) {
            this.fields.tax_3 = entity.tax_3
            const a_total = parseFloat(entity.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(entity.tax_3) / 100
            tax_total += tax_percentage
        }

        return tax_total
    }

    calculateTaxes (usesInclusiveTaxes) {
        let tax_total = 0

        if (this.fields.tax_rate > 0) {
            const a_total = parseFloat(this.fields.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_rate) / 100
            tax_total += tax_percentage
        }

        if (this.fields.tax_2 && this.fields.tax_2 > 0) {
            const a_total = parseFloat(this.fields.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_2) / 100
            tax_total += tax_percentage
        }

        if (this.fields.tax_3 && this.fields.tax_3 > 0) {
            const a_total = parseFloat(this.fields.amount)
            const tax_percentage = parseFloat(a_total) * parseFloat(this.fields.tax_3) / 100
            tax_total += tax_percentage
        }

        return Math.round(tax_total, 2)
    }

    calculateTax (tax_amount) {
        const a_total = parseFloat(this.fields.total)
        const tax_percentage = parseFloat(a_total) * parseFloat(tax_amount) / 100

        return Math.round(tax_percentage, 2)
    } */
}
