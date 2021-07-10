import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class CompanyGatewayRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/company_gateways'
        this.entity = 'Invoice'
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

    async stripeImport (token) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(this._url + '/stripe/import', { token: token })

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

    async createStripeAccount (token) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(this._url + '/stripe/connect', { token: token })

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

    async getGateways () {
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
