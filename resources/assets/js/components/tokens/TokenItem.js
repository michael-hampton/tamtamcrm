import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditToken from './edit/EditToken'
import { Input, ListGroupItem } from 'reactstrap'
import TokenPresenter from '../presenters/TokenPresenter'

export default class TokenItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteToken = this.deleteToken.bind(this)
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

    deleteToken (id, archive = false) {
        const url = archive === true ? `/api/tokens/archive/${id}` : `/api/tokens/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrTokens = [...self.props.tokens]
                const index = arrTokens.findIndex(token => token.id === id)
                arrTokens.splice(index, 1)
                self.props.addUserToState(arrTokens)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { tokens, ignoredColumns } = this.props
        if (tokens && tokens.length) {
            return tokens.map((token, index) => {
                const restoreButton = token.deleted_at
                    ? <RestoreModal id={token.id} entities={tokens} updateState={this.props.addUserToState}
                        url={`/api/tokens/restore/${token.id}`}/> : null
                const deleteButton = !token.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteToken} id={token.id}/> : null
                const archiveButton = !token.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteToken} id={token.id}/> : null

                const editButton = !token.deleted_at ? <EditToken
                    tokens={tokens}
                    token={token}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(token).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(token, token.name, editButton)}
                        data-label={key}><TokenPresenter edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={token}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(token.id)
                const selectedRow = this.props.viewId === token.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={token.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={token.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={token.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(token, token.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<TokenPresenter field="name"
                                entity={token}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5><br/>
                            <span className="mb-1">{<TokenPresenter field="user_id"
                                entity={token}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={token.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(token, token.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<TokenPresenter field="name"
                                entity={token}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{<TokenPresenter field="user_id"
                                entity={token}
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
