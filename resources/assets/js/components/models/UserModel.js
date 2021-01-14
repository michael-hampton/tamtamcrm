import axios from 'axios'
import BaseModel from './BaseModel'

export const user_pdf_fields = [
    '$user.first_name', '$user.last_name', '$user.phone', '$user.email', '$user.custom1',
    '$user.custom2', '$user.custom3', '$user.custom4'
]

export default class UserModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/users'
        this.entity = 'User'
        this.users = []

        this._fields = {
            modal: false,
            name: '',
            target_url: 'http://',
            event_id: '',
            loading: false,
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

    async getUsers () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this.url)

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }

            this.users = res.data
            // Don't forget to return something
            return res.data
        } catch (e) {
            this.handleError(e)
            return false
        }
    }
}
