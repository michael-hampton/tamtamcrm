import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class CustomerTypeDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            customerTypes: []
        }

        this.getCustomerTypes = this.getCustomerTypes.bind(this)
    }

    componentDidMount () {
        if (!this.props.customerTypes || !this.props.customerTypes.length) {
            this.getCustomerTypes()
        }
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

    getCustomerTypes () {
        axios.get('/api/customer-types')
            .then((r) => {
                this.setState({
                    customerTypes: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let customerTypeList = null
        if (!this.state.customerTypes.length) {
            customerTypeList = <option value="">Loading...</option>
        } else {
            customerTypeList = this.state.customerTypes.map((customerType, index) => (
                <option key={index} value={customerType.id}>{customerType.name}</option>
            ))
        }

        return (
            <FormGroup>
                <Input value={this.props.customer_type} onChange={this.props.handleInputChanges} type="select"
                    name="customer_type" id="customer_type">
                    <option value="">{translations.select_option}</option>
                    {customerTypeList}
                </Input>
                {this.renderErrorFor('customer_type')}
            </FormGroup>
        )
    }
}
