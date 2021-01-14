import axios from 'axios'
import BaseModel from './BaseModel'
import TaskModel from './TaskModel'

export default class ProjectModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/projects'
        this.entity = 'Project'

        this._fields = {
            number: '',
            id: null,
            modal: false,
            name: '',
            description: '',
            customer_id: '',
            private_notes: '',
            public_notes: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            due_date: '',
            assigned_to: '',
            budgeted_hours: '',
            task_rate: '',
            column_color: '',
            count: 2,
            errors: [],
            customers: [],
            tasks: []
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }

        this._file_count = 0

        if (data !== null && data.files) {
            this.fileCount = data.files
        }
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
        }

        if (this.isModuleEnabled('invoices') && !this.fields.deleted_at) {
            actions.push('projectToInvoice')
        }

        if (this.isModuleEnabled('tasks') && !this.fields.deleted_at) {
            actions.push('projectToTask')
        }

        if (this.isModuleEnabled('credits') && !this.fields.deleted_at) {
            actions.push('newCredit')
        }

        if (this.isModuleEnabled('invoices') && !this.fields.deleted_at) {
            actions.push('newInvoice')
        }

        if (this.isModuleEnabled('quotes') && !this.fields.deleted_at) {
            actions.push('newQuote')
        }

        if (this.isModuleEnabled('recurringInvoices') && !this.fields.deleted_at) {
            actions.push('newRecurringInvoice')
        }

        if (this.isModuleEnabled('recurringQuotes') && !this.fields.deleted_at) {
            actions.push('newRecurringQuote')
        }

        if (this.isModuleEnabled('expenses') && !this.fields.deleted_at) {
            actions.push('newExpense')
        }

        return actions
    }

    taskDurationForProject () {
        let total = 0
        this.fields.tasks.map(task => {
            if (task.is_active && task.project_id === parseInt(this.fields.id)) {
                const taskModel = new TaskModel(task)
                total += taskModel.getTotalDuration()
            }
        })

        return total
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

    calculateAmount () {
        const total_duration = this.fields.budgeted_hours

        if (!total_duration) {
            return 0
        }

        const duration = this.fields.task_rate * total_duration
        return Math.round(duration, 3)
    }
}
