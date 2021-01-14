import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditGroup from './edit/EditGroup'
import { Input, ListGroupItem } from 'reactstrap'

export default class GroupItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteGroup = this.deleteGroup.bind(this)
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

    deleteGroup (id, archive = false) {
        const url = archive === true ? `/api/groups/archive/${id}` : `/api/groups/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrGroups = [...self.props.groups]
                const index = arrGroups.findIndex(group => group.id === id)
                arrGroups.splice(index, 1)
                self.props.addUserToState(arrGroups)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { groups, ignoredColumns } = this.props
        if (groups && groups.length) {
            return groups.map((group, index) => {
                const restoreButton = group.deleted_at
                    ? <RestoreModal id={group.id} entities={groups} updateState={this.props.addUserToState}
                        url={`/api/groups/restore/${group.id}`}/> : null
                const deleteButton = !group.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteGroup} id={group.id}/> : null
                const archiveButton = !group.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteGroup} id={group.id}/> : null

                const editButton = !group.deleted_at ? <EditGroup
                    modal={true}
                    groups={groups}
                    group={group}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(group).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td onClick={() => this.props.toggleViewedEntity(group, group.name)} data-label={key}
                        key={key}>{group[key]}</td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(group.id)
                const selectedRow = this.props.viewId === group.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={group.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={group.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={group.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(group, group.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{group.name}</h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={group.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(group, group.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{group.name}</h5>
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
