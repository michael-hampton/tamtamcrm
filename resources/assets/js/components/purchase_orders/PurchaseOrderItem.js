import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditPurchaseOrder from './edit/EditPurchaseOrder'
import PurchaseOrderPresenter from '../presenters/PurchaseOrderPresenter'

export default class PurchaseOrderItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deletePurchaseOrder = this.deletePurchaseOrder.bind(this)
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

    deletePurchaseOrder (id, archive = false) {
        const url = archive === true ? `/api/purchase_order/archive/${id}` : `/api/purchase_order/${id}`
        const self = this
        axios.delete(url).then(function (response) {
            const arrQuotes = [...self.props.purchase_orders]
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
        const { purchase_orders, custom_fields, companies } = this.props
        if (purchase_orders && purchase_orders.length && companies.length) {
            return purchase_orders.map((purchase_order, index) => {
                const restoreButton = purchase_order.deleted_at && !purchase_order.is_deleted
                    ? <RestoreModal id={purchase_order.id} entities={purchase_orders}
                        updateState={this.props.updateInvoice}
                        url={`/api/purchase_order/restore/${purchase_order.id}`}/> : null

                const deleteButton = !purchase_order.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deletePurchaseOrder}
                        id={purchase_order.id}/> : null

                const archiveButton = !purchase_order.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deletePurchaseOrder}
                        id={purchase_order.id}/> : null

                const editButton = !purchase_order.deleted_at ? <EditPurchaseOrder
                    custom_fields={custom_fields}
                    companies={companies}
                    modal={true}
                    add={false}
                    invoice={purchase_order}
                    invoice_id={purchase_order.id}
                    action={this.props.updateInvoice}
                    invoices={purchase_orders}
                /> : null

                const columnList = Object.keys(purchase_order).filter(key => {
                    return this.props.ignoredColumns && !this.props.ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(purchase_order, purchase_order.number, editButton)}
                        data-label={key}><PurchaseOrderPresenter companies={companies}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={purchase_order}
                            edit={editButton}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(purchase_order.id)
                const selectedRow = this.props.viewId === purchase_order.id ? 'table-row-selected' : ''
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
                            <Input checked={isChecked} className={checkboxClass} value={purchase_order.id}
                                type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={purchase_order.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(purchase_order, purchase_order.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="col-4"><PurchaseOrderPresenter companies={companies} field="company_id"
                                entity={purchase_order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span className="col-4">{purchase_order.number} . <PurchaseOrderPresenter
                                field={purchase_order.due_date.length ? 'due_date' : 'date'} entity={purchase_order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span className="col-2">
                                <PurchaseOrderPresenter companies={companies}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={purchase_order.balance > 0 ? 'balance' : 'total'}
                                    entity={purchase_order} edit={editButton}/>
                            </span>
                            <span className="col-2"><PurchaseOrderPresenter field="status_field" entity={purchase_order}
                                edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={purchase_order.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(purchase_order, purchase_order.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5><PurchaseOrderPresenter companies={companies} field="company_id"
                                entity={purchase_order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span>
                                <PurchaseOrderPresenter companies={companies}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={purchase_order.balance > 0 ? 'balance' : 'total'}
                                    entity={purchase_order} edit={editButton}/>
                            </span>

                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span>{purchase_order.number} . <PurchaseOrderPresenter
                                field={purchase_order.due_date.length ? 'due_date' : 'date'} entity={purchase_order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span>{<PurchaseOrderPresenter field="status_field" entity={purchase_order}
                                edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/>}</span>
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
