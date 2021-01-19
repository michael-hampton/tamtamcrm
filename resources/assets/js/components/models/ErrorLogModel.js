import BaseModel from './BaseModel'

export default class ErrorLogModel extends BaseModel {
    constructor (data = null) {
        super()

        this._fields = {
            data: '',
            error_type: '',
            error_result: '',
            entity: '',
            entity_id: '',
            account_id: '',
            user_id: '',
            customer_id: ''
        }

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }

        this.PAYMENT = 'payment'
        this.EMAIL = 'email'

        this.SUCCESS = 'success'
        this.NEUTRAL = 'neutral'
        this.FAILURE = 'failure'

        this.ENTITY_STRIPE = 'stripe'
        this.ENTITY_AUTHORIZE = 'authorize'
        this.ENTITY_PAYPAL = 'paypal'
    }

    get fields () {
        return this._fields
    }

    get category () {
        switch (this.fields.error_type) {
            case this.PAYMENT:
                return 'payment'
            case this.EMAIL:
                return 'email'
        }

        return ''
    }

    get event () {
        switch (this.fields.error_result) {
            case this.NEUTRAL:
                return ''
            case this.SUCCESS:
                return 'error_log_success'
            case this.FAILURE:
                return 'error_log_failure'
        }

        return ''
    }

    get entity () {
        if (this.fields.error_type !== 'payment') {
            return this.fields.entity
        }

        const gateways = JSON.parse(localStorage.getItem('gateways'))

        const gateway = gateways.filter(gateway => gateway.key === this.fields.entity)

        return gateway.length ? gateway[0].name : ''
    }
}
