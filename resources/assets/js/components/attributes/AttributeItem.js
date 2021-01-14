import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditAttribute from './edit/EditAttribute'
import { Input } from 'reactstrap'

export default class AttributeItem extends Component {
    constructor (props) {
        super(props)

        this.deleteAttribute = this.deleteAttribute.bind(this)
    }

    deleteAttribute (id, archive = false) {
        const url = archive === true ? `/api/attributes/archive/${id}` : `/api/attributes/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrAttributes = [...self.props.attributes]
                const index = arrAttributes.findIndex(attribute => attribute.id === id)
                arrAttributes.splice(index, 1)
                self.props.addUserToState(arrAttributes)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { attributes, ignoredColumns } = this.props
        if (attributes && attributes.length) {
            return attributes.map(attribute => {
                const restoreButton = attribute.deleted_at
                    ? <RestoreModal id={attribute.id} entities={attributes} updateState={this.props.addUserToState}
                        url={`/api/attributes/restore/${attribute.id}`}/> : null
                const deleteButton = !attribute.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteAttribute} id={attribute.id}/> : null
                const archiveButton = !attribute.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteAttribute} id={attribute.id}/> : null

                const editButton = !attribute.deleted_at ? <EditAttribute
                    attributes={attributes}
                    attribute={attribute}
                    action={this.props.addUserToState}
                /> : null

                const attribute_values = Array.prototype.map.call(attribute.values, function (item) {
                    return item.value
                }).join(',')

                console.log('attributes', attribute_values)

                const columnList = Object.keys(attribute).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <td onClick={() => this.props.toggleViewedEntity(attribute, attribute.name)}
                        data-label={key}
                        key={key}>{key === 'name' ? `${attribute[key]} (${attribute_values})` : attribute[key]}</td>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(attribute.id)
                const selectedRow = this.props.viewId === attribute.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={attribute.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={attribute.id} type="checkbox"
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
