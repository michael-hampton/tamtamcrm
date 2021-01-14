import React, { Component } from 'react'
import { Input } from 'reactstrap'
import Select from 'react-select'
import { translations } from '../../utils/_translations'
import CustomerRepository from '../../repositories/CustomerRepository'

export default class CustomerDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            customers: []
        }

        this.getCustomers = this.getCustomers.bind(this)
    }

    componentDidMount () {
        if (!this.props.customers || !this.props.customers.length) {
            this.getCustomers()
        } else {
            this.props.customers.unshift({ id: '', name: 'Select Customer' })
            this.setState({ customers: this.props.customers })
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

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
                this.state.customers.unshift({ id: '', name: 'Select Customer' })
            })
        })
    }

    handleChange (value, name) {
        const e = {
            target: {
                id: name,
                name: name,
                value: value.id
            }
        }

        this.props.handleInputChanges(e)
    }

    render () {
        const customerList = <option value="">Select Customer</option>
        let options = null

        if (this.state.customers && this.state.customers.length) {
            options = this.state.customers.map((customer, index) => (
                <option key={index} value={customer.id}>{customer.name}</option>
            ))
        }

        const customer = this.props.customer ? this.state.customers.filter(option => option.id === this.props.customer) : null

        const name = 'customer_id'
        const selectList = this.props.disabled
            ? <Input disabled value={this.props.customer} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>{customerList}{options}</Input> : <Select
                placeholder={translations.select_option}
                className="flex-grow-1"
                classNamePrefix="select"
                name={name}
                value={customer}
                options={this.state.customers}
                getOptionLabel={option => option.name}
                getOptionValue={option => option.id}
                onChange={(value) => this.handleChange(value, name)}
            />

        return (
            <React.Fragment>
                {selectList}
                {this.renderErrorFor('customer_id')}
            </React.Fragment>
        )
    }
}
