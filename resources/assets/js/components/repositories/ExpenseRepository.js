import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class ExpenseRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/expenses'
        this._category_url = '/api/expense-categories'
        this.entity = 'Invoice'
    }

    async getById (id) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(`${this._url}/${id}`)

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

    async get (status = null, customer_id = null) {
        this.errors = []
        this.error_message = ''

        const parameters = {}

        if (customer_id !== null) {
            parameters.customer_id = customer_id
        }

        if (status !== null) {
            parameters.status = status
        }

        const url = Object.keys(parameters).length ? this._url + `?${this.buildQueryParams(parameters)}` : this._url

        try {
            const res = await axios.get(url)

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

    async getCategories () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this._category_url)

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
