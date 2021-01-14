import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditTaskStatus from './edit/EditTaskStatus'
import { Input, ListGroupItem } from 'reactstrap'
import TaskStatusPresenter from '../presenters/TaskStatusPresenter'

export default class TaskStatusItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteTaskStatus = this.deleteTaskStatus.bind(this)
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

    deleteTaskStatus (id, archive = false) {
        const url = archive === true ? `/api/taskStatus/archive/${id}` : `/api/statuses/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrTaskStatuss = [...self.props.statuses]
                const index = arrTaskStatuss.findIndex(taskStatus => taskStatus.id === id)
                arrTaskStatuss.splice(index, 1)
                self.props.addUserToState(arrTaskStatuss)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { statuses, ignoredColumns, customers } = this.props
        if (statuses && statuses.length) {
            return statuses.map((taskStatus, index) => {
                const restoreButton = taskStatus.deleted_at
                    ? <RestoreModal id={taskStatus.id} entities={statuses} updateState={this.props.addUserToState}
                        url={`/api/statuses/restore/${taskStatus.id}`}/> : null
                const deleteButton = !taskStatus.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteTaskStatus} id={taskStatus.id}/> : null
                const archiveButton = !taskStatus.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteTaskStatus} id={taskStatus.id}/> : null

                const editButton = !taskStatus.deleted_at ? <EditTaskStatus
                    statuses={statuses}
                    customers={customers}
                    task_status={taskStatus}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(taskStatus).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(taskStatus, taskStatus.name, editButton)}
                        data-label={key}><TaskStatusPresenter toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={taskStatus} edit={editButton}/>
                    </td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(taskStatus.id)
                const selectedRow = this.props.viewId === taskStatus.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={taskStatus.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={taskStatus.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={taskStatus.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(taskStatus, taskStatus.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><TaskStatusPresenter field="name"
                                entity={taskStatus}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={taskStatus.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(taskStatus, taskStatus.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><TaskStatusPresenter field="name"
                                entity={taskStatus}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
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
