import axios from 'axios'
import BaseModel from './BaseModel'

export default class PlanModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/plans'
        this.entity = 'Plan'

        this._fields = {
            modal: false,
            name: '',
            code: '',
            description: '',
            price: 0,
            interval_unit: 'year',
            interval_count: 1,
            trial_period: 0,
            invoice_period: 1,
            invoice_interval: 'month',
            grace_period: 10,
            grace_interval: 'day',
            active_subscribers_limit: 1,
            trial_interval: 'day',
            auto_billing_enabled: false,
            can_cancel_plan: true,
            account_id: JSON.parse(localStorage.getItem('appState')).user.account_id,
            loading: false,
            errors: [],
            message: ''
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

    buildDropdownMenu () {
        const actions = []

        if (!this.fields.hide) {
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
