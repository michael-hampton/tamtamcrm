import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import { Input, ListGroupItem } from 'reactstrap'
import BankAccountPresenter from '../presenters/BankAccountPresenter'
import EditBankAccount from './edit/EditBankAccount'

export default class BankAccountItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteToken = this.deleteToken.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ width: window.innerWidth })
    }

    deleteToken (id, archive = false) {
        const url = archive === true ? `/api/bank_accounts/archive/${id}` : `/api/bank_accounts/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrTokens = [...self.props.entities]
                const index = arrTokens.findIndex(bank_account => bank_account.id === id)
                arrTokens.splice(index, 1)
                self.props.addUserToState(arrTokens)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { bank_accounts, ignoredColumns, entities } = this.props
        if (bank_accounts && bank_accounts.length) {
            return bank_accounts.map((bank_account, index) => {
                const restoreButton = bank_account.deleted_at
                    ? <RestoreModal id={bank_account.id} entities={entities} updateState={this.props.addUserToState}
                        url={`/api/bank_accounts/restore/${bank_account.id}`}/> : null
                const deleteButton = !bank_account.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteToken} id={bank_account.id}/> : null
                const archiveButton = !bank_account.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteToken} id={bank_account.id}/> : null

                const editButton = !bank_account.deleted_at ? <EditBankAccount
                    bank_accounts={entities}
                    bank_account={bank_account}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(bank_account).filter(key => {
                    return ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(bank_account, bank_account.name, editButton)}
                        data-label={key}><BankAccountPresenter edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={bank_account}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(bank_account.id)
                const selectedRow = this.props.viewId === bank_account.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={bank_account.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={bank_account.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={bank_account.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(bank_account, bank_account.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<BankAccountPresenter field="name"
                                entity={bank_account}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5><br/>
                            <span className="mb-1">{<BankAccountPresenter field="user_id"
                                entity={bank_account}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={bank_account.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(bank_account, bank_account.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<BankAccountPresenter field="name"
                                entity={bank_account}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{<BankAccountPresenter field="user_id"
                                entity={bank_account}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
                        </div>
                    </ListGroupItem>
                </div>
            })
        } else {
            return <tr>
                <td className="text-center">No Records Found.</td>
            </tr>
        }
    }
}
