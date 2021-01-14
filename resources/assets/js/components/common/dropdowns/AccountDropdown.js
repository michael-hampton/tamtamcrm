import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'

export default class AccountDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            accounts: []
        }

        this.getAccounts = this.getAccounts.bind(this)
    }

    componentDidMount () {
        if (!this.props.accounts || !this.props.accounts.length) {
            this.getAccounts()
        } else {
            this.setState({ accounts: this.props.accounts })
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

    getAccounts () {
        axios.get('/api/accounts')
            .then((r) => {
                this.setState({
                    accounts: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let accountList = null
        if (!this.state.accounts.length) {
            accountList = <option value="">Loading...</option>
        } else {
            accountList = this.state.accounts.map((account, index) => (
                <option key={index} value={account.id}>{account.settings.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'account_id'

        return (
            <FormGroup className="mr-2">
                <Input value={this.props.account} onChange={this.props.handleInputChanges} type="select" multiple
                    name={name} id={name}>
                    {accountList}
                </Input>
            </FormGroup>
        )
    }
}
