import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class PaymentTypeDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            paymentTypes: []
        }

        this.getPaymentTypes = this.getPaymentTypes.bind(this)
    }

    componentDidMount () {
        if (!this.props.paymentTypes || !this.props.paymentTypes.length) {
            this.getPaymentTypes()
        }
    }

    getPaymentTypes () {
        axios.get('/api/paymentType')
            .then((r) => {
                this.setState({
                    paymentTypes: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback d-block'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
    }

    render () {
        let paymentList = null

        if (!this.state.paymentTypes.length) {
            paymentList = <option value="">Loading...</option>
        } else {
            paymentList = this.state.paymentTypes.map((paymentType, index) => (
                <option key={index} value={paymentType.id}>{paymentType.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'payment_type'

        return (
            <FormGroup>
                <Input value={this.props.payment_type} onChange={this.props.handleInputChanges} type="select"
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    {paymentList}
                </Input>
                {this.renderErrorFor(name)}
            </FormGroup>
        )
    }
}
