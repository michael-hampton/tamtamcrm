import React, { Component } from 'react'
import axios from 'axios'
import { Input, ListGroupItem } from 'reactstrap'
import RestoreModal from '../common/RestoreModal'
import ActionsMenu from '../common/ActionsMenu'
import UserPresenter from '../presenters/UserPresenter'
import { translations } from '../utils/_translations'
import ConfirmPassword from '../common/ConfirmPassword'
import { icons } from '../utils/_icons'
import AddUser from './edit/AddUser'

export default class UserItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteUser = this.deleteUser.bind(this)
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

    deleteUser (id, password, archive = false) {
        const data = {
            password: password
        }

        const url = archive === true ? `/api/users/archive/${id}` : `/api/users/${id}`
        const self = this
        axios.delete(url, { data: data })
            .then(function (response) {
                const arrUsers = [...self.props.entities]
                const index = arrUsers.findIndex(user => user.id === parseInt(id))
                arrUsers[index].is_deleted = archive !== true
                arrUsers[index].deleted_at = new Date()
                self.props.addUserToState(arrUsers, true)
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
        const { users, departments, custom_fields, ignoredColumns, entities } = this.props

        if (users && users.length) {
            return users.map((user, index) => {
                const restoreButton = user.deleted_at
                    ? <RestoreModal id={user.id} entities={entities} updateState={this.props.addUserToState}
                        url={`/api/users/restore/${user.id}`}/> : null
                const archiveButton = !user.deleted_at
                    ? <ConfirmPassword dropdown={true} icon={icons.archive} id={user.id} callback={(id, password) => {
                        this.deleteUser(id, password, true)
                    }
                    } text={translations.archive_message} button_color="btn-danger"
                    button_label={translations.archive}/> : null
                const deleteButton = !user.deleted_at
                    ? <ConfirmPassword dropdown={true} icon={icons.delete} id={user.id} callback={(id, password) => {
                        this.deleteUser(id, password, false)
                    }
                    } text={translations.delete_message} button_color="btn-link"
                    button_label={translations.delete}/> : null
                const editButton = !user.deleted_at
                    ? <AddUser add={false} accounts={this.props.accounts} departments={departments} user={user}
                        user_id={user.id}
                        custom_fields={custom_fields} users={entities}
                        action={this.props.addUserToState}/> : null

                const columnList = Object.keys(user).filter(key => {
                    return ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(user, user.name, editButton)}
                        data-label={key}><UserPresenter edit={editButton}
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
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(user, `${user.first_name} ${user.last_name}`, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<UserPresenter field="name"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                            <span className="mb-1 text-muted">{<UserPresenter field="email"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
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
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(user, `${user.first_name} ${user.last_name}`, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<UserPresenter field="name"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{<UserPresenter field="email"
                                entity={user}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
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
