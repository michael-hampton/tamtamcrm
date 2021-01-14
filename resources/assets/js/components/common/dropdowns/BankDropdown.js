import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class BankDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            banks: []
        }

        this.getBanks = this.getBanks.bind(this)
    }

    componentDidMount () {
        if (!this.props.banks || !this.props.banks.length) {
            this.getBanks()
        } else {
            this.setState({ banks: this.props.banks })
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

    getBanks () {
        axios.get('/api/banks')
            .then((r) => {
                this.setState({
                    banks: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    buuildMultiple (name, bankList) {
        return (
            <Input value={this.props.bank_id} onChange={this.props.handleInputChanges} type="select" multiple
                name={name} id={name}>
                {bankList}
            </Input>
        )
    }

    buildSingle (name, bankList) {
        return (
            <Input value={this.props.bank_id} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {bankList}
            </Input>
        )
    }

    render () {
        let bankList = null
        if (!this.state.banks.length) {
            bankList = <option value="">Loading...</option>
        } else {
            bankList = this.state.banks.map((bank, index) => (
                <option key={index} value={bank.id}>{bank.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'bank_id'
        const input = this.props.multiple && this.props.multiple === true ? this.buuildMultiple(name, bankList) : this.buildSingle(name, bankList)

        return (
            <FormGroup>
                {input}
                {this.renderErrorFor('bank_id')}
            </FormGroup>
        )
    }
}
