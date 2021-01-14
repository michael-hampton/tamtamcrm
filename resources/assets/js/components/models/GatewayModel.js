import axios from 'axios'
import BaseModel from './BaseModel'
import { consts } from '../utils/_consts'

export default class GatewayModel extends BaseModel {
    constructor (data = null) {
        super()

        this._url = '/api/company_gateways'
        this.entity = 'Gateway'

        this.account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(this.account_id))
        this.settings = user_account[0].account.settings

        this._fields = {
            modal: false,
            name: '',
            description: '',
            gateway_key: '',
            accepted_credit_cards: 0,
            accepted_cards: new Map(),
            require_cvv: false,
            required_fields: new Map(),
            update_details: true,
            config: '',
            fees_and_limits: [],
            token_billing: true,
            loading: false,
            errors: [],
            activeTab: '1'
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }
    }

    get fields () {
        return this._fields
    }

    get url () {
        return this._url
    }

    get gateway_ids () {
        return this.settings.company_gateway_ids || ''
    }

    set gateway_ids (ids) {
        this.settings.company_gateway_ids = ids
        this.fields.company_gateways_ids = ids
    }

    get supportsTokenBilling () {
        return [
            kGatewayStripe,
            kGatewayAuthorizeNet,
            kGatewayCheckoutCom
        ].includes(id)
    }

    buildDropdownMenu () {
        const actions = []

        if (!this.fields.is_deleted) {
            actions.push('delete')
        }

        if (!this.fields.deleted_at) {
            actions.push('archive')
        }

        return actions
    }

    supportsCard (cardType) {
        return this.fields.accepted_credit_cards & cardType > 0
    }

    performAction () {

    }

    async update (data) {
        if (!this.fields.id) {
            return false
        }

        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(`${this.url}/${this.fields.id}`, data)

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

    async getGateways () {
        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.get(this.url)

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

    async completeAction (data, action) {
        if (!this.fields.id) {
            return false
        }

        this.errors = []
        this.error_message = ''

        try {
            const res = await axios.post(`${this.url}/${this.fields.id}/${action}`, data)

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

    async save (data) {
        if (this.fields.id) {
            return this.update(data)
        }

        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post(this.url, data)

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

    getClientUrl (gateway_key, customer_reference) {
        switch (gateway_key) {
            case consts.stripe_gateway:
                return `https://dashboard.stripe.com/customers/${customer_reference}`
            default:
                return null
        }
    }

    getPaymentUrl (gateway_key, reference_number) {
        switch (gateway_key) {
            case consts.stripe_gateway:
                return `https://dashboard.stripe.com/payments/${reference_number}`
            default:
                return null
        }
    }
}
