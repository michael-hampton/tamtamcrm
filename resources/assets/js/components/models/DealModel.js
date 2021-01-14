import axios from 'axios'
import moment from 'moment'
import BaseModel from './BaseModel'

export default class DealModel extends BaseModel {
    constructor (data = null, customers) {
        super()
        this.customers = customers
        this.entity = 'Deal'
        this._url = '/api/deals'

        this._fields = {
            modal: false,
            design_id: '',
            name: '',
            rating: '',
            source_type: 0,
            errors: [],
            valued_at: '',
            customer_id: '',
            assigned_to: '',
            content: '',
            contributors: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            activeTab: '1',
            custom_value4: '',
            public_notes: '',
            private_notes: '',
            timers: [],
            due_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            start_date: moment(new Date()).add(1, 'days').format('YYYY-MM-DD'),
            task_status_id: null,
            project_id: null,
            loading: false,
            users: [],
            selectedUsers: []
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

    set start_date (start_date) {
        this.fields.start_date = moment(start_date, 'YYYY-MM-DD')
    }

    set due_date (due_date) {
        this.fields.due_date = moment(due_date, 'YYYY-MM-DD')
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    buildDropdownMenu () {
        const actions = []

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
            actions.push('cloneToTask')
            actions.push('cloneToLead')
            actions.push('cloneToProject')
        }

        if (this.fields.customer_id.toString().length) {
            actions.push('pdf')
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
}
