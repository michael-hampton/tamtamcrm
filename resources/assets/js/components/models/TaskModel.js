import axios from 'axios'
import moment from 'moment'
import BaseModel from './BaseModel'

export default class TaskModel extends BaseModel {
    constructor (data = null, customers) {
        super()
        this.customers = customers
        this.entity = 'Task'
        this._url = '/api/tasks'

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.account = user_account[0]

        this._fields = {
            account_id: account_id,
            number: '',
            modal: false,
            task_rate: 0,
            design_id: '',
            name: '',
            assigned_to: '',
            errors: [],
            customer_id: '',
            description: '',
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
            task_status: null,
            project_id: null,
            loading: false,
            users: [],
            selectedUsers: [],
            is_recurring: false,
            recurring_start_date: '',
            recurring_end_date: '',
            recurring_due_date: '',
            last_sent_date: '',
            next_send_date: '',
            recurring_frequency: 0,
            include_documents: this.settings.include_task_documents || false
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

    get id () {
        return this.fields.id
    }

    get duration () {
        return this.fields.duration
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get isRunning () {
        if (this.fields.timers && this.fields.timers.length) {
            const last_element = this.fields.timers[this.fields.timers.length - 1]
            return !last_element.end_date || !last_element.end_date.length
        }

        return false
    }

    get isNew () {
        return !this.fields.id || !this.fields.id.toString().length || parseInt(this.fields.id) <= 0
    }

    set customer_id (customer_id) {
        this.fields.customer_id = customer_id
    }

    calculateAmount (taskRate) {
        const total_duration = this.duration

        if (!total_duration) {
            return 0
        }

        const duration = taskRate * total_duration
        return Math.round(duration, 3)
    }

    getTotalDuration () {
        let seconds = 0
        this.fields.timers.map(timer => {
            seconds += this.calculateDuration(timer.start_time, timer.end_time, true)
        })

        return seconds
    }

    calculateDuration (currentStartTime, currentEndTime, returnAsSeconds = false) {
        const startTime = moment(currentStartTime, 'YYYY-MM-DD hh:mm:ss a')
        let endTime = ''

        if (currentEndTime.length) {
            endTime = moment(currentEndTime, 'YYYY-MM-DD hh:mm:ss a')
            let hours = (endTime.diff(startTime, 'hours'))
            const totalMinutes = endTime.diff(startTime, 'minutes')
            const totalSeconds = endTime.diff(startTime, 'seconds')
            const minutes = totalMinutes % 60
            const clearMinutes = ('0' + minutes).slice(-2)

            if (returnAsSeconds === true) {
                const duration = parseFloat(hours + '.' + minutes)
                return duration * 3600
            }

            hours = (hours < 10 ? '0' : '') + hours

            return `${hours}:${clearMinutes}:${totalSeconds}`
        }

        return ''
    }

    buildDropdownMenu () {
        const actions = []

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
            actions.push('cloneTaskToDeal')
        }

        if (!this.fields.is_deleted) {
            actions.push('newInvoice')
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

    formatDuration (duration, showSeconds = true) {
        const time = duration.toString().split('.')[0]

        if (showSeconds) {
            return time
        }

        const parts = time.split(':')
        return `${parts[0]}:${parts[1]}`
    }

    formatTime (secs) {
        let hours = Math.floor(secs / (60 * 60))

        const divisor_for_minutes = secs % (60 * 60)
        let minutes = Math.floor(divisor_for_minutes / 60)

        const divisor_for_seconds = divisor_for_minutes % 60
        let seconds = Math.ceil(divisor_for_seconds)

        seconds = (seconds < 10 ? '0' : '') + seconds
        minutes = (minutes < 10 ? '0' : '') + minutes
        hours = (hours < 10 ? '0' : '') + hours

        return `${hours} : ${minutes} : ${seconds}`
    }
}
