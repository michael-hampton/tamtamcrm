import axios from 'axios'
import React, { Component } from 'react'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditDeal from './edit/EditDeal'
import DealPresenter from '../presenters/DealPresenter'

export default class DealItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteDeal = this.deleteDeal.bind(this)
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

    deleteDeal (id, archive = false) {
        const self = this
        const url = archive === true ? `/api/deals/archive/${id}` : `/api/deals/${id}`

        axios.delete(url)
            .then(function (response) {
                const arrDeals = [...self.props.deals]
                const index = arrDeals.findIndex(deal => deal.id === id)
                arrDeals.splice(index, 1)
                self.props.addUserToState(arrDeals)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { deals, custom_fields, users, ignoredColumns, customers } = this.props
        if (deals && deals.length && users.length) {
            return deals.map((deal, index) => {
                const restoreButton = deal.deleted_at
                    ? <RestoreModal id={deal.id} entities={deals} updateState={this.props.addUserToState}
                        url={`/api/deals/restore/${deal.id}`}/> : null
                const archiveButton = !deal.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteDeal} id={deal.id}/> : null
                const deleteButton = !deal.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteDeal} id={deal.id}/> : null
                const editButton = !deal.deleted_at ? <EditDeal
                    modal={true}
                    listView={true}
                    custom_fields={custom_fields}
                    users={users}
                    deal={deal}
                    deals={deals}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(deal).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key} onClick={() => this.props.toggleViewedEntity(deal, deal.number, editButton)}
                        data-label={key}><DealPresenter toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={deal} custom_fields={custom_fields}
                            edit={editButton}
                            users={users}
                            customers={this.props.customers}
                            deals={deals}
                            action={this.props.action}
                            deal={deal}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(deal.id)
                const selectedRow = this.props.viewId === deal.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={deal.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={deal.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={deal.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(deal, deal.name, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="col-4">{<DealPresenter customers={customers} field="name" entity={deal}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                            <span className="col-4"><DealPresenter field="customer_id" customers={customers}
                                entity={deal}
                                edit={editButton}/>
                            <br/>
                            {!!deal.project && deal.project.name &&
                                <DealPresenter field="project" entity={deal}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    edit={editButton}/>
                            }
                            </span>
                            <span className="col-2">
                                <DealPresenter customers={customers}
                                    field="due_date" entity={deal}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    edit={editButton}/>
                            </span>
                            <span className="col-2"><DealPresenter field="status_field" entity={deal}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={deal.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem key={index}
                        onClick={() => this.props.toggleViewedEntity(deal, deal.name, editButton)}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<DealPresenter customers={customers} field="name" entity={deal}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                            {<DealPresenter customers={customers}
                                field="due_date" entity={deal}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{<DealPresenter field="customer_id" customers={customers}
                                entity={deal}
                                edit={editButton}/>} </span>
                            <span>{<DealPresenter field="status_field" entity={deal}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</span>
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
