import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditPayment from './edit/EditPayment'
import PaymentPresenter from '../presenters/PaymentPresenter'
import Refund from './edit/Refund'
import PaymentModel from '../models/PaymentModel'

export default class PaymentItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deletePayment = this.deletePayment.bind(this)
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

    deletePayment (id, archive = true) {
        const url = archive === true ? `/api/payments/archive/${id}` : `/api/payments/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrPayments = [...self.props.payments]
                const index = arrPayments.findIndex(payment => payment.id === id)
                arrPayments.splice(index, 1)
                self.props.updateCustomers(arrPayments)
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
        const { payments, custom_fields, invoices, customers, credits } = this.props

        if (payments && payments.length && customers.length && invoices.length && credits.length) {
            return payments.map((payment, index) => {
                const paymentModel = new PaymentModel(invoices, payment, credits)
                const paymentableInvoices = invoices && invoices.length ? paymentModel.paymentableInvoices : null
                const paymentableCredits = credits && credits.length ? paymentModel.paymentableCredits : null

                const restoreButton = paymentModel.isDeleted
                    ? <RestoreModal id={payment.id} entities={payments} updateState={this.props.updateCustomers}
                        url={`/api/payments/restore/${payment.id}`}/> : null

                const archiveButton = paymentModel.isActive
                    ? <DeleteModal archive={true} deleteFunction={this.deletePayment} id={payment.id}/> : null

                const deleteButton = paymentModel.isActive || paymentModel.isArchived
                    ? <DeleteModal archive={false} deleteFunction={this.deletePayment} id={payment.id}/> : null

                const editButton = !payment.deleted_at ? <EditPayment
                    custom_fields={custom_fields}
                    invoices={invoices}
                    credits={credits}
                    payment={payment}
                    action={this.props.updateCustomers}
                    payments={payments}
                    customers={customers}
                    modal={true}
                /> : null

                const columnList = Object.keys(payment).filter(key => {
                    return this.props.ignoredColumns && !this.props.ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(payment, payment.number, editButton)}
                        data-label={key}><PaymentPresenter customers={customers} field={key}
                            paymentables={paymentableInvoices}
                            paymentable_credits={paymentableCredits}
                            entity={payment} edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}/>
                    </td>
                })

                const refundButton = paymentableInvoices.length && invoices.length
                    ? <Refund customers={customers} payment={payment}
                        modal={true}
                        allInvoices={paymentableInvoices}
                        allCredits={paymentableCredits}
                        invoices={invoices}
                        credits={credits}
                        payments={payments}
                        paymentables={payment.paymentables}
                        action={this.props.updateCustomers}/> : null

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(payment.id)
                const selectedRow = this.props.viewId === payment.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} refund={refundButton} edit={editButton}
                        delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={index}>
                        <td>
                            {!!this.props.onChangeBulk &&
                            <Input checked={isChecked} className={checkboxClass} value={payment.id} type="checkbox"
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
                        <Input checked={isChecked} className={checkboxClass} value={payment.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(payment, payment.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><PaymentPresenter customers={customers} field="customer_id"
                                entity={payment}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <span><PaymentPresenter customers={customers}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                field="amount" entity={payment} edit={editButton}/></span>
                            <span className="mb-1">{payment.number} . <PaymentPresenter field="date"
                                entity={payment}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><PaymentPresenter field="status_field" entity={payment} edit={editButton}
                                toggleViewedEntity={this.props.toggleViewedEntity}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={payment.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(payment, payment.number, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><PaymentPresenter customers={customers} field="customer_id"
                                entity={payment}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <PaymentPresenter customers={customers}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                field="amount" entity={payment} edit={editButton}/>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{payment.number} . <PaymentPresenter field="date"
                                entity={payment}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><PaymentPresenter field="status_field" entity={payment} edit={editButton}
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
