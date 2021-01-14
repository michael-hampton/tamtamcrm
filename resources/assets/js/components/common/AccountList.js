import React, { Component } from 'react'
import { FormGroup, Input } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'

export default class AccountList extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            check: false,
            errors: [],
            showSuccessMessage: false,
            showErrorMessage: false,
            message: '',
            account_id: Object.prototype.hasOwnProperty.call(localStorage, 'account_id') ? localStorage.getItem('account_id') : ''
        }

        this.toggle = this.toggle.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    handleChange (e) {
        const accountId = e.target.value

        if (accountId === 'add') {
            window.location.href = '/#/accounts/true'
            return
        }

        axios.post('/api/account/change', { account_id: e.target.value })
            .then(function (response) {
                localStorage.setItem('account_id', accountId)
                location.reload()
            })
            .catch(function (error) {
                alert(error)
                console.log(error)
            })
    }

    render () {
        let accounts = false
        if (Object.prototype.hasOwnProperty.call(localStorage, 'appState')) {
            accounts = JSON.parse(localStorage.appState).accounts
        }

        const columnList = accounts !== false ? accounts.map(account => {
            return <option key={account.account.id} value={account.account.id}>{account.account.settings.name}</option>
        }) : null

        const number_of_accounts = localStorage.getItem('number_of_accounts')

        return (
            <React.Fragment>
                <FormGroup style={{ width: '90%' }} className="mt-1 ml-2">
                    <Input value={this.state.account_id} type="select" onChange={this.handleChange} name="account_id"
                        id="account_id">
                        {columnList}

                        {!!number_of_accounts < 10 &&
                        <option value="add">{translations.add_account}</option>
                        }
                    </Input>
                </FormGroup>
            </React.Fragment>
        )
    }
}
