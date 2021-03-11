import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import { Input, ListGroupItem } from 'reactstrap'
import EditCaseTemplate from './edit/EditCaseTemplate'
import CaseTemplatePresenter from '../presenters/CaseTemplatePresenter'

export default class CaseTemplateItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteTemplate = this.deleteTemplate.bind(this)
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

    deleteTemplate (id, archive = false) {
        const url = archive === true ? `/api/case_template/archive/${id}` : `/api/case_template/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrTemplates = [...self.props.entities]
                const index = arrTemplates.findIndex(case_template => case_template.id === id)
                arrTemplates[index].is_deleted = archive !== true
                arrTemplates[index].deleted_at = new Date()
                self.props.addUserToState(arrTemplates, true)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { case_templates, ignoredColumns, entities } = this.props
        if (case_templates && case_templates.length) {
            return case_templates.map((case_template, index) => {
                const restoreButton = case_template.deleted_at
                    ? <RestoreModal id={case_template.id} entities={entities} updateState={this.props.addUserToState}
                        url={`/api/case_template/restore/${case_template.id}`}/> : null
                const deleteButton = !case_template.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteTemplate} id={case_template.id}/> : null
                const archiveButton = !case_template.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteTemplate} id={case_template.id}/> : null

                const editButton = !case_template.deleted_at ? <EditCaseTemplate
                    templates={entities}
                    template={case_template}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(case_template).filter(key => {
                    return ignoredColumns.includes(key)
                }).map(key => {
                    return <td key={key}
                        onClick={() => this.props.toggleViewedEntity(case_template, case_template.name, editButton)}
                        data-label={key}><CaseTemplatePresenter edit={editButton}
                            toggleViewedEntity={this.props.toggleViewedEntity}
                            field={key} entity={case_template}/></td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(case_template.id)
                const selectedRow = this.props.viewId === case_template.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={case_template.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={case_template.id}
                                type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return !is_mobile && !this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={case_template.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(case_template, case_template.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<CaseTemplatePresenter field="name"
                                entity={case_template}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5><br/>
                            <span className="mb-1">{<CaseTemplatePresenter field="send_on"
                                entity={case_template}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>} </span>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={case_template.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>
                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(case_template, case_template.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{<CaseTemplatePresenter field="name"
                                entity={case_template}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                edit={editButton}/>}</h5>
                        </div>
                        <div className="d-flex w-100 justify-content-between">
                            <span className="mb-1 text-muted">{<CaseTemplatePresenter field="send_on"
                                entity={case_template}
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
