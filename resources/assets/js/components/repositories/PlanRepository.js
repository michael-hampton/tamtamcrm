import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class PlanRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/plan_subscriptions'
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

    async cancel (subscription) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(`${this._url}/cancel/${subscription}`)

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

    async renew (subscription) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(`${this._url}/renew/${subscription}`)

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

    async plans () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get('/api/plans')

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
