import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class InvoiceRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/invoice'
        this.entity = 'Invoice'
    }

    async get (status = null) {
        const url = status !== null ? `api/invoice?status=${status}` : this._url
        this.errors = []
        this.error_message = ''

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
}
