import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditSubscription from './edit/EditSubscription'
import { Input, ListGroupItem } from 'reactstrap'
import SubscriptionPresenter from '../presenters/SubscriptionPresenter'

export default class SubscriptionItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteSubscription = this.deleteSubscription.bind(this)
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

    deleteSubscription (id, archive = false) {
        const url = archive === true ? `/api/subscriptions/archive/${id}` : `/api/subscriptions/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrSubscriptions = [...self.props.subscriptions]
                const index = arrSubscriptions.findIndex(subscription => subscription.id === id)
                arrSubscriptions.splice(index, 1)
                self.props.addUserToState(arrSubscriptions)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { subscriptions, ignoredColumns } = this.props
        if (subscriptions && subscriptions.length) {
            return subscriptions.map((subscription, index) => {
                const restoreButton = subscription.deleted_at
                    ? <RestoreModal id={subscription.id} entities={subscriptions} updateState={this.props.addUserToState}
                        url={`/api/subscriptions/restore/${subscription.id}`}/> : null
                const deleteButton = !subscription.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteSubscription} id={subscription.id}/> : null
                const archiveButton = !subscription.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteSubscription} id={subscription.id}/> : null

                const editButton = !subscription.deleted_at ? <EditSubscription
                    subscriptions={subscriptions}
                    subscription={subscription}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(subscription).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(subscription, subscription.target_url, editButton)}
                        data-label={key}><SubscriptionPresenter
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={subscription} edit={editButton}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(subscription.id)
                const selectedRow = this.props.viewId === subscription.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={subscription.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={subscription.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={subscription.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(subscription, subscription.target_url, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><SubscriptionPresenter field="name"
                                entity={subscription}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>
                            </h5>
                            <h5 className="mb-1"><SubscriptionPresenter field="target_url"
                                entity={subscription}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>
                            </h5>
                            <h5>
                                <SubscriptionPresenter field="event_id"
                                    entity={subscription}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    edit={editButton}/></h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={subscription.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(subscription, subscription.target_url, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><SubscriptionPresenter field="target_url"
                                entity={subscription}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/> .
                            <SubscriptionPresenter field="event_id"
                                entity={subscription}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>
                            </h5>
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
