import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class AccountRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/accounts'
    }

    async backupData () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post('/api/account/backup')

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

    async getById (id) {
        this.errors = []
        this.error_message = ''

        try {
            const url = `${this._url}/${id}`
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

    async checkDomain (domain) {
        this.errors = []
        this.error_message = ''

        try {
            const url = `${this._url}/check-domain/${domain}`
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
