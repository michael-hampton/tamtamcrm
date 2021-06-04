import axios from 'axios'
import BaseRepository from './BaseRepository'
import { buildPdf } from '../utils/Pdf'

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

    async getTemplates () {
        this.errors = []
        this.error_message = ''

        try {
            const url = '/api/email_templates'
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

    async getReminders () {
        this.errors = []
        this.error_message = ''

        try {
            const url = 'api/reminders'
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

    async previewPdf (show_html = false, entity = null, entity_id = null, design = null, design_id = null) {
        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post('/api/preview', { entity: entity, entity_id: entity_id, show_html: show_html, design: design, design_id: design_id })

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }

            // Don't forget to return something

            if (show_html === true) {
                return res.data
            }

            return buildPdf(res.data)
        } catch (e) {
            alert(e)
            this.handleError(e)
            return false
        }
    }
}
