import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class TaskRepository extends BaseRepository {
    constructor () {
        super()

        this._url = 'api/taskStatus'
        this.entity = 'Invoice'
    }

    async get (task_type) {
        this.errors = []
        this.error_message = ''
        const url = `${this._url}?task_type=${task_type}`

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
