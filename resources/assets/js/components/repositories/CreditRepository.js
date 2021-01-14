import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class CreditRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/credits'
        this.entity = 'Invoice'
    }

    async get (status = null) {
        this.errors = []
        this.error_message = ''
        const url = status !== null ? `api/credits?status=${status}` : this._url

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
