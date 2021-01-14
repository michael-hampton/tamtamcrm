import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class AccountRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/accounts'
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
}
