import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class CustomerRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/customers'
        this.entity = 'Invoice'
    }

    async get () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this._url + '?status=active')

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

    async transactions (customer) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this._url + `/transactions/${customer}`)

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

    async gatewayTokens (customer) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(`/api/customers/gateway_tokens/${customer}`)

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

    async error_logs (customer) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this._url + `/error_logs/${customer}`)

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
