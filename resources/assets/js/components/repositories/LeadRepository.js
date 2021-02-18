import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class LeadRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/leads'
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

    async updateSortOrder (tasks) {
        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post(`${this._url}/sort`, { tasks: tasks })

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
