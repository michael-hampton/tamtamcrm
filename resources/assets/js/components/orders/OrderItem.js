import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditOrder from './edit/EditOrder'
import OrderPresenter from '../presenters/OrderPresenter'

export default class OrderItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteOrder = this.deleteOrder.bind(this)
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

    deleteOrder (id, archive = false) {
        const url = archive === true ? `/api/order/archive/${id}` : `/api/order/${id}`
        const self = this
        axios.delete(url).then(function (response) {
            const arrQuotes = [...self.props.orders]
            const index = arrQuotes.findIndex(payment => payment.id === id)
            arrQuotes.splice(index, 1)
            self.props.updateOrder(arrQuotes)
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
        const { orders, customers, custom_fields } = this.props
        if (orders && orders.length && customers.length) {
            return orders.map((order, index) => {
                const restoreButton = order.deleted_at && !order.is_deleted
                    ? <RestoreModal id={order.id} entities={orders} updateState={this.props.updateOrder}
                        url={`/api/order/restore/${order.id}`}/> : null

                const archiveButton = !order.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteOrder} id={order.id}/> : null

                const deleteButton = !order.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteOrder} id={order.id}/> : null

                const editButton = !order.deleted_at ? <EditOrder
                    custom_fields={custom_fields}
                    customers={customers}
                    modal={true}
                    add={false}
                    order={order}
                    order_id={order.id}
                    action={this.props.updateOrder}
                    orders={orders}
                /> : null

                const columnList = Object.keys(order).filter(key => {
                    return this.props.ignoredColumns && !this.props.ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(order, order.number, editButton)}
                        data-label={key}><OrderPresenter customers={customers}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={order} edit={editButton}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(order.id)
                const selectedRow = this.props.viewId === order.id ? 'table-row-selected' : ''
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
                            <Input checked={isChecked} className={checkboxClass} value={order.id} type="checkbox"
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
                        <Input checked={isChecked} className={checkboxClass} value={order.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(order, order.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="col-4"><OrderPresenter customers={customers} field="customer_id"
                                entity={order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span className="col-4">{order.number} . {<OrderPresenter
                                field={order.due_date.length ? 'due_date' : 'date'} entity={order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
                            <span className="col-2">
                                <OrderPresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={order.balance > 0 ? 'balance' : 'total'} entity={order}
                                    edit={editButton}/>
                            </span>
                            <span className="col-2"><OrderPresenter field="status_field" entity={order}
                                edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={order.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(order, order.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><OrderPresenter customers={customers} field="customer_id"
                                entity={order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span>
                                <OrderPresenter customers={customers}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    field={order.balance > 0 ? 'balance' : 'total'} entity={order}
                                    edit={editButton}/>
                            </span>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{order.number} . <OrderPresenter
                                field={order.due_date.length ? 'due_date' : 'date'} entity={order}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><OrderPresenter field="status_field" entity={order} edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
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
