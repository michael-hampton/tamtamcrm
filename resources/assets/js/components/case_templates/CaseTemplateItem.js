import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditCaseTemplate from './edit/EditCaseTemplate'
import { Input } from 'reactstrap'
import CaseTemplatePresenter from '../presenters/CaseTemplatePresenter'

export default class CaseTemplateItem extends Component {
    constructor (props) {
        super(props)

        this.deleteCaseTemplate = this.deleteCaseTemplate.bind(this)
    }

    deleteCaseTemplate (id, archive = false) {
        const url = archive === true ? `/api/case_template/archive/${id}` : `/api/case_template/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrTemplates = [...self.props.templates]
                const index = arrTemplates.findIndex(template => template.id === id)
                arrTemplates.splice(index, 1)
                self.props.addUserToState(arrTemplates)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { templates, ignoredColumns, customers } = this.props
        if (templates && templates.length) {
            return templates.map(template => {
                const restoreButton = template.deleted_at
                    ? <RestoreModal id={template.id} entities={templates} updateState={this.props.addUserToState}
                        url={`/api/case_template/restore/${template.id}`}/> : null
                const deleteButton = !template.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteCaseTemplate} id={template.id}/> : null
                const archiveButton = !template.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteCaseTemplate} id={template.id}/> : null

                const editButton = !template.deleted_at ? <EditCaseTemplate
                    templates={templates}
                    template={template}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(template).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <CaseTemplatePresenter key={key} customers={customers} edit={editButton}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={template}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(template.id)
                const selectedRow = this.props.viewId === template.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={template.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={template.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        {actionMenu}
                    </td>
                    {columnList}
                </tr>
            })
        } else {
            return <tr>
                <td className="text-center">No Records Found.</td>
            </tr>
        }
    }
}
