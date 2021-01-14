import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class BankAccountDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            bank_accounts: []
        }

        this.getBankAccounts = this.getBankAccounts.bind(this)
    }

    componentDidMount () {
        if (!this.props.bank_accounts || !this.props.bank_accounts.length) {
            this.getBankAccounts()
        } else {
            this.setState({ bank_accounts: this.props.bank_accounts })
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

    getBankAccounts () {
        axios.get('/api/bank_accounts')
            .then((r) => {
                this.setState({
                    bank_accounts: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    buuildMultiple (name, bankAccountList) {
        return (
            <Input value={this.props.bank_account} onChange={this.props.handleInputChanges} type="select" multiple
                name={name} id={name}>
                {bankAccountList}
            </Input>
        )
    }

    buildSingle (name, bankAccountList) {
        return (
            <Input value={this.props.bank_account} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {bankAccountList}
            </Input>
        )
    }

    render () {
        let bankAccountList = null
        if (!this.state.bank_accounts.length) {
            bankAccountList = <option value="">Loading...</option>
        } else {
            bankAccountList = this.state.bank_accounts.map((bank_account, index) => (
                <option key={index} value={bank_account.id}>{bank_account.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'bank_account_id'
        const input = this.props.multiple && this.props.multiple === true ? this.buuildMultiple(name, bankAccountList) : this.buildSingle(name, bankAccountList)

        return (
            <FormGroup>
                {input}
                {this.renderErrorFor('bank_account_id')}
            </FormGroup>
        )
    }
}
