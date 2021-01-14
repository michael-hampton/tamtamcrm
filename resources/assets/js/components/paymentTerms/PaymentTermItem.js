import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditPaymentTerm from './edit/EditPaymentTerm'
import { Input, ListGroupItem } from 'reactstrap'

export default class PaymentTermItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deletePaymentTerm = this.deletePaymentTerm.bind(this)
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

    deletePaymentTerm (id, archive = false) {
        const url = archive === true ? `/api/payment_terms/archive/${id}` : `/api/payment_terms/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrPaymentTerms = [...self.props.paymentTerms]
                const index = arrPaymentTerms.findIndex(payment_term => payment_term.id === id)
                arrPaymentTerms.splice(index, 1)
                self.props.addUserToState(arrPaymentTerms)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { paymentTerms, ignoredColumns } = this.props
        if (paymentTerms && paymentTerms.length) {
            return paymentTerms.map((payment_term, index) => {
                const restoreButton = payment_term.deleted_at
                    ? <RestoreModal id={payment_term.id} entities={paymentTerms} updateState={this.props.addUserToState}
                        url={`/api/payment_terms/restore/${payment_term.id}`}/> : null
                const deleteButton = !payment_term.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deletePaymentTerm} id={payment_term.id}/> : null
                const archiveButton = !payment_term.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deletePaymentTerm} id={payment_term.id}/> : null

                const editButton = !payment_term.deleted_at ? <EditPaymentTerm
                    payment_terms={paymentTerms}
                    payment_term={payment_term}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(payment_term).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td onClick={() => this.props.toggleViewedEntity(payment_term, payment_term.name)}
                        data-label={key}
                        key={key}>{payment_term[key]}</td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(payment_term.id)
                const selectedRow = this.props.viewId === payment_term.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={payment_term.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={payment_term.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={payment_term.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(payment_term, payment_term.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{payment_term.name}</h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={payment_term.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(payment_term, payment_term.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{payment_term.name}</h5>
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
