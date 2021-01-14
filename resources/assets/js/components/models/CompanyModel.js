import axios from 'axios'
import BaseModel from './BaseModel'

export default class CompanyModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/companies'
        this.entity = 'Company'

        this._fields = {
            id: null,
            modal: false,
            number: '',
            name: '',
            logo: '',
            website: '',
            phone_number: '',
            email: '',
            address_1: '',
            currency_id: null,
            assigned_to: null,
            industry_id: '',
            country_id: null,
            company_logo: null,
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            private_notes: '',
            public_notes: '',
            address_2: '',
            town: '',
            city: '',
            postcode: '',
            loading: false,
            errors: [],
            contacts: [],
            selectedUsers: [],
            message: '',
            activeTab: '1'
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }
    }

    get fileCount () {
        return this._file_count || 0
    }

    set fileCount (files) {
        this._file_count = files ? files.length : 0
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get hasCurrency () {
        return this.fields.currency_id != null && this.fields.currency_id.toString().length
    }

    get currencyId () {
        if (!this.fields.currency_id) {
            return null
        }

        return parseInt(this.fields.currency_id)
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
            const res = await axios.post(`${this.url}/${this.fields.id}`, data)

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

    async getCompanies () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this._url)

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
