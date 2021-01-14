import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditInvoice from './edit/EditInvoice'
import InvoicePresenter from '../presenters/InvoicePresenter'

export default class InvoiceItem extends Component {
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
        const url = archive === true ? `/api/invoice/archive/${id}` : `/api/invoice/${id}`
        const self = this
        axios.delete(url).then(function (response) {
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
        const { invoices, customers, custom_fields } = this.props

        if (invoices && invoices.length && customers.length) {
            return invoices.map((invoice, index) => {
                const restoreButton = invoice.deleted_at && !invoice.is_deleted
                    ? <RestoreModal id={invoice.id} entities={invoices} updateState={this.props.updateInvoice}
                        url={`/api/invoice/restore/${invoice.id}`}/> : null

                const archiveButton = !invoice.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteInvoice} id={invoice.id}/> : null

                const deleteButton = !invoice.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteInvoice} id={invoice.id}/> : null

                const editButton = !invoice.deleted_at ? <EditInvoice
                    custom_fields={custom_fields}
                    customers={customers}
                    modal={true}
                    add={false}
                    invoice={invoice}
                    invoice_id={invoice.id}
                    action={this.props.updateInvoice}
                    invoices={invoices}
                /> : null

                const columnList = Object.keys(invoice).filter(key => {
                    return this.props.ignoredColumns && !this.props.ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(invoice, invoice.number, editButton)}
                        data-label={key}><InvoicePresenter customers={customers}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={invoice} edit={editButton}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(invoice.id)
                const selectedRow = this.props.viewId === invoice.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={index}>
                        <td>
                            {!!this.props.onChangeBulk &&
                            <Input checked={isChecked} className={checkboxClass} value={invoice.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            }
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={invoice.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(invoice, invoice.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="col-4"><InvoicePresenter customers={customers} field="customer_id"
                                entity={invoice}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span className="col-4">{invoice.number} . <InvoicePresenter
                                field={invoice.due_date.length ? 'due_date' : 'date'} entity={invoice}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span className="col-2">
                                <InvoicePresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={invoice.balance > 0 ? 'balance' : 'total'} entity={invoice}
                                    edit={editButton}/>
                            </span>
                            <span className="col-2"><InvoicePresenter field="status_field" entity={invoice}
                                edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={invoice.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(invoice, invoice.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><InvoicePresenter customers={customers} field="customer_id"
                                entity={invoice}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span>
                                <InvoicePresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={invoice.balance > 0 ? 'balance' : 'total'} entity={invoice}
                                    edit={editButton}/>
                            </span>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{invoice.number} . <InvoicePresenter
                                field={invoice.due_date.length ? 'due_date' : 'date'} entity={invoice}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><InvoicePresenter field="status_field" entity={invoice} edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
                        </div>
                    </ListGroupItem>
                </div>
            })
        } else {
            return <div>
                <span className="text-center">No Records Found.</span>
            </div>
        }
    }
}
