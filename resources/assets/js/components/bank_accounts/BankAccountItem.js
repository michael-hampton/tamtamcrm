import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditBankAccount from './edit/EditBankAccount'
import BankAccountPresenter from '../presenters/BankAccountPresenter'

export default class BankAccountItem extends Component {
    constructor (props) {
        super(props)

        this.deleteBankAccount = this.deleteBankAccount.bind(this)
    }

    deleteBankAccount (id, archive = false) {
        const self = this
        const url = archive === true ? `/api/bank_accounts/archive/${id}` : `/api/bank_accounts/${id}`

        axios.delete(url)
            .then(function (response) {
                const arrBankAccounts = [...self.props.bank_accounts]
                const index = arrBankAccounts.findIndex(bank_account => bank_account.id === id)
                arrBankAccounts.splice(index, 1)
                self.props.addUserToState(arrBankAccounts)
            })
            .catch(function (error) {
                console.log(error)
                self.setState(
                    {
                        error: error.response.data
                    }
                )
            })
    }

    render () {
        const { bank_accounts, custom_fields, ignoredColumns } = this.props
        if (bank_accounts && bank_accounts.length) {
            return bank_accounts.map(bank_account => {
                const restoreButton = bank_account.deleted_at && !bank_account.is_deleted
                    ? <RestoreModal id={bank_account.id} entities={bank_accounts} updateState={this.props.addUserToState}
                        url={`/api/bank_accounts/restore/${bank_account.id}`}/> : null
                const archiveButton = !bank_account.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteBankAccount} id={bank_account.id}/> : null
                const deleteButton = !bank_account.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteBankAccount} id={bank_account.id}/> : null
                const editButton = !bank_account.deleted_at ? <EditBankAccount
                    listView={true}
                    custom_fields={custom_fields}
                    bank_account={bank_account}
                    bank_accounts={bank_accounts}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(bank_account).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <BankAccountPresenter key={key} edit={editButton}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={bank_account}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(bank_account.id)
                const selectedRow = this.props.viewId === bank_account.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={bank_account.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={bank_account.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        {actionMenu}
                    </td>
                    {columnList}
                </tr>
            })
        } else {
            return <tr>
                <td className="text-center">No Records Found.</td>
            </tr>
        }
    }
}
