import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditCase from './edit/EditCase'
import { Input, ListGroupItem } from 'reactstrap'
import CasePresenter from '../presenters/CasePresenter'

export default class CaseItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteCase = this.deleteCase.bind(this)
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

    deleteCase (id, archive = false) {
        const url = archive === true ? `/api/cases/archive/${id}` : `/api/cases/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrCases = [...self.props.cases]
                const index = arrCases.findIndex(case_file => case_file.id === id)
                arrCases.splice(index, 1)
                self.props.addUserToState(arrCases)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { cases, ignoredColumns, customers } = this.props
        if (cases && cases.length) {
            return cases.map((case_file, index) => {
                const restoreButton = case_file.deleted_at
                    ? <RestoreModal id={case_file.id} entities={cases} updateState={this.props.addUserToState}
                        url={`/api/cases/restore/${case_file.id}`}/> : null
                const deleteButton = !case_file.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteCase} id={case_file.id}/> : null
                const archiveButton = !case_file.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteCase} id={case_file.id}/> : null

                const editButton = !case_file.deleted_at ? <EditCase
                    cases={cases}
                    customers={customers}
                    case={case_file}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(case_file).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(case_file, case_file.subject, editButton)}
                        data-label={key}><CasePresenter key={key} customers={customers} edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={case_file}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(case_file.id)
                const selectedRow = this.props.viewId === case_file.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={case_file.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={case_file.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={case_file.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(case_file, case_file.subject, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><CasePresenter customers={customers} field="subject"
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <CasePresenter customers={customers}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                field="due_date" entity={case_file} edit={editButton}/>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted"><CasePresenter field="customer_id" customers={customers}
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>

                            <span className="mb-1"><CasePresenter field="priority_field"
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><CasePresenter field="status_field" entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={case_file.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(case_file, case_file.subject, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1"><CasePresenter customers={customers} field="subject"
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></h5>
                            <CasePresenter customers={customers}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                field="due_date" entity={case_file} edit={editButton}/>
                            <span className="mb-1"><CasePresenter field="customer_id" customers={customers}
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>

                            <span className="mb-1"><CasePresenter field="priority_field"
                                entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
                            <span><CasePresenter field="status_field" entity={case_file}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/></span>
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
