import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class CustomerGroupDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            customerGroups: []
        }

        this.getCustomerGroups = this.getCustomerGroups.bind(this)
    }

    componentDidMount () {
        if (!this.props.customerGroups || !this.props.customerGroups.length) {
            this.getCustomerGroups()
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

    getCustomerGroups () {
        axios.get('/api/groups')
            .then((r) => {
                this.setState({
                    customerGroups: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let customerGroupList = null
        if (!this.state.customerGroups.length) {
            customerGroupList = <option value="">Loading...</option>
        } else {
            customerGroupList = this.state.customerGroups.map((customerGroup, index) => (
                <option key={index} value={customerGroup.id}>{customerGroup.name}</option>
            ))
        }

        return (
            <FormGroup>
                <Input value={this.props.customer_group} onChange={this.props.handleInputChanges} type="select"
                    name="group_settings_id" id="group_settings_id">
                    <option value="">{translations.select_option}</option>
                    {customerGroupList}
                </Input>
                {this.renderErrorFor('event_type')}
            </FormGroup>
        )
    }
}
