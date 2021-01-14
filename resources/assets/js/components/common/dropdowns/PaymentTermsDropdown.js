import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class PaymentTermsDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            paymentTerms: []
        }

        this.getPaymentTerms = this.getPaymentTerms.bind(this)
    }

    componentDidMount () {
        if (!this.props.paymentTerms || !this.props.paymentTerms.length) {
            this.getPaymentTerms()
        }
    }

    getPaymentTerms () {
        axios.get('/api/payment_terms')
            .then((r) => {
                this.setState({
                    paymentTerms: r.data
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

        if (!this.state.paymentTerms.length) {
            paymentList = <option value="">Loading...</option>
        } else {
            paymentList = this.state.paymentTerms.map((paymentTerm, index) => (
                <option key={index} value={paymentTerm.number_of_days}>{paymentTerm.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'payment_term'

        return (
            <FormGroup>
                <Input data-namespace="settings" value={this.props.payment_term}
                    onChange={this.props.handleInputChanges} type="select"
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    {paymentList}
                </Input>
                {this.renderErrorFor(name)}
            </FormGroup>
        )
    }
}
