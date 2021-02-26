import axios from 'axios'
import BaseRepository from './BaseRepository'

export default class UserRepository extends BaseRepository {
    constructor () {
        super()

        this._url = '/api/users'
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

    async confirmEmail (user) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(`/api/user/verify/${user}`)

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

    async forgotPassword (email) {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(`/api/login/forgot-password`, { email:email })

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
