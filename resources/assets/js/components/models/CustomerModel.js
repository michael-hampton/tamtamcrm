import axios from 'axios'
import BaseModel from './BaseModel'

export const customer_pdf_fields = ['$customer.name', '$customer.number', '$customer.vat_number', '$customer.address1', '$customer.address2', '$customer.city_state_postal',
    '$customer.postal_city_state', '$customer.country', '$contact.email', '$customer.custom1', '$customer.custom2', '$customer.custom3',
    '$customer.custom4'
]

class ContactModel {
    constructor (contact) {
        this._contact = contact

        this._fields = {
            id: '',
            settings: {},
            first_name: '',
            last_name: '',
            phone: '',
            email: '',
            is_primary: false,
            contact_key: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            errors: []
        }

        if (contact !== null) {
            this._fields = { ...this.fields, ...contact }
        }
    }

    get fullName () {
        return (this._fields.first_name + ' ' + this._fields.last_name).trim()
    }

    get fullNameWithEmail () {
        let name = this.fullName

        if (this._fields.email.length) {
            if (!name.length) {
                name += this._fields.email
            } else {
                name += `<${this._fields.email}>`
            }
        }

        return name
    }

    get id () {
        return this._fields.id
    }
}

export default class CustomerModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/customers'
        this.entity = 'Customer'

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }

        this._fields = {
            currency_id: '',
            settings: {},
            id: null,
            modal: false,
            name: '',
            default_payment_method: null,
            group_id: null,
            phone: '',
            address_1: '',
            address_2: '',
            company__id: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            zip: '',
            city: '',
            description: '',
            values: [],
            contacts: [],
            gateway_tokens: [],
            loading: false,
            submitSuccess: false,
            count: 2,
            errors: []
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get fileCount () {
        return this._file_count || 0
    }

    set fileCount (files) {
        this._file_count = files ? files.length : 0
    }

    get gateway_ids () {
        return this.settings.company_gateway_ids || ''
    }

    set gateway_ids (ids) {
        this.settings.company_gateway_ids = ids
        this.fields.settings.company_gateways_ids = ids
    }

    get gateways () {
        if (!this.fields.settings) {
            return []
        }

        if (this.fields.settings.company_gateway_ids && typeof this.fields.settings.company_gateway_ids === 'string') {
            return this.fields.settings.company_gateway_ids.split(',').map(Number)
        }

        return this.fields.settings.company_gateway_ids || []
    }

    get hasLanguage () {
        return this.fields.settings && this.fields.settings.language_id != null && this.fields.settings.language_id.toString().length
    }

    get languageId () {
        if (!this.fields.settings || !this.fields.settings.language_id) {
            return null
        }

        return parseInt(this.fields.settings.language_id)
    }

    get hasCurrency () {
        return this.fields.currency_id != null && this.fields.currency_id.toString().length
    }

    get currencyId () {
        if (!this.fields.currency_id || !this.fields.currency_id) {
            return null
        }

        return parseInt(this.fields.currency_id)
    }

    get gateway_tokens () {
        return this.fields.gateway_tokens
    }

    get displayName () {
        return this.fields.name
    }

    hasEmailAddress () {
        const has_email = this.fields.contacts && this.fields.contacts.length ? this.fields.contacts.filter(contact => contact.email && contact.email.length) : []
        return has_email.length > 0
    }

    buildDropdownMenu () {
        const actions = []

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
        }

        return actions
    }

    performAction () {

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

    findContact (contact_id) {
        console.log('contacts 2', this.fields.contacts)
        const contact = this.fields.contacts.filter(contact => contact.id === parseInt(contact_id))

        if (!contact.length) {
            return false
        }

        return new ContactModel(contact[0])
    }

    addGateway (gateway) {
        const company_gateway_ids = this.gateways
        company_gateway_ids.push(parseInt(gateway))
        this.fields.settings.company_gateway_ids = company_gateway_ids

        return company_gateway_ids
    }

    removeGateway (gateway) {
        let company_gateway_ids = this.gateways
        company_gateway_ids = company_gateway_ids.filter(item => item !== parseInt(gateway))
        this.settings.company_gateway_ids = company_gateway_ids
        this.fields.settings.company_gateway_ids = company_gateway_ids
        return company_gateway_ids
    }

    async saveSettings () {
        if (this.settings.company_gateway_ids && this.settings.company_gateway_ids.length) {
            this.fields.settings.company_gateway_ids = this.settings.company_gateway_ids.join(',')
        }

        this.save({ name: this.fields.name, settings: this.fields.settings }).then(response => {
            return response
        })
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
}
