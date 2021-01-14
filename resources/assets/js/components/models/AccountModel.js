import axios from 'axios'
import BaseModel from './BaseModel'

export const account_pdf_fields = [
    '$account.name', '$account.id_number', '$account.vat_number', '$account.website', '$account.email', '$account.account1',
    '$account.account2', '$account.account3', '$account.account4'
]

export default class AccountModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/accounts'
        this.entity = 'Account'

        this._fields = {
            modal: false,
            id: null,
            account_id: null,
            name: '',
            activeTab: '1',
            settings: {},
            loading: false,
            changesMade: false,
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

        alert('here')

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

        this.save({ settings: JSON.stringify(this.fields.settings) }).then(response => {
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
