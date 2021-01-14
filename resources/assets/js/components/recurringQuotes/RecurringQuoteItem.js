import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import UpdateRecurringQuote from './edit/UpdateRecurringQuote'
import RecurringQuotePresenter from '../presenters/RecurringQuotePresenter'

export default class RecurringQuoteItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteInvoice = this.deleteInvoice.bind(this)
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

    deleteInvoice (id, archive = false) {
        const url = archive === true ? `/api/recurring-quote/archive/${id}` : `/api/recurring-quote/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrQuotes = [...self.props.invoices]
                const index = arrQuotes.findIndex(payment => payment.id === id)
                arrQuotes.splice(index, 1)
                self.props.updateInvoice(arrQuotes)
            })
            .catch(function (error) {
                self.setState(
                    {
                        error: error.response.data
                    }
                )
            })
    }

    render () {
        const { invoices, custom_fields, customers, allQuotes } = this.props
        if (invoices && invoices.length && customers.length) {
            return invoices.map((user, index) => {
                const restoreButton = user.deleted_at
                    ? <RestoreModal id={user.id} entities={invoices} updateState={this.props.updateInvoice}
                        url={`/api/recurringQuote/restore/${user.id}`}/> : null
                const archiveButton = !user.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteInvoice} id={user.id}/> : null

                const deleteButton = !user.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteInvoice} id={user.id}/> : null

                const editButton = !user.deleted_at ? <UpdateRecurringQuote
                    allQuotes={allQuotes}
                    custom_fields={custom_fields}
                    customers={customers}
                    modal={true}
                    add={false}
                    invoice={user}
                    invoice_id={user.id}
                    action={this.props.updateInvoice}
                    invoices={invoices}
                /> : null

                const columnList = Object.keys(user).filter(key => {
                    return this.props.ignoredColumns && !this.props.ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(user, user.number, editButton)}
                        data-label={key}><RecurringQuotePresenter customers={customers} edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={user}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(user.id)
                const selectedRow = this.props.viewId === user.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={user.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={user.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={user.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem onClick={() => this.props.toggleViewedEntity(user, user.number, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="col-4"><RecurringQuotePresenter customers={customers} field="customer_id"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span className="col-4">{user.number} . <RecurringQuotePresenter field="due_date"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span className="col-2">
                                <RecurringQuotePresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={user.balance > 0 ? 'balance' : 'total'} entity={user}
                                    edit={editButton}/>
                            </span>
                            <span className="col-2"><RecurringQuotePresenter field="status_field" entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={user.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem onClick={() => this.props.toggleViewedEntity(user, user.number, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><RecurringQuotePresenter customers={customers} field="customer_id"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span>
                                <RecurringQuotePresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={user.balance > 0 ? 'balance' : 'total'} entity={user}
                                    edit={editButton}/>
                            </span>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{user.number} . <RecurringQuotePresenter field="due_date"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><RecurringQuotePresenter field="status_field" entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
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
