import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class TaskRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/tasks'
        this._status_url = 'api/taskStatus'
        this.entity = 'Invoice'
    }

    async get (status = null, customer_id = null, project_id = null) {
        this.errors = []
        this.error_message = ''

        const parameters = {}

        if (customer_id !== null) {
            parameters.customer_id = customer_id
        }

        if (status !== null) {
            parameters.status = status
        }

        if (project_id !== null) {
            parameters.project_id = project_id
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

    async getStatuses (task_type) {
        this.errors = []
        this.error_message = ''
        const url = `${this._status_url}?task_type=${task_type}`

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
