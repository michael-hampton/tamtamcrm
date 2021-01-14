import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditCategory from './edit/EditCategory'
import { Input } from 'reactstrap'
import CategoryPresenter from '../presenters/CategoryPresenter'

export default class CategoryItem extends Component {
    constructor (props) {
        super(props)

        this.deleteCategory = this.deleteCategory.bind(this)
    }

    deleteCategory (id, archive = false) {
        const url = archive === true ? `/api/categories/archive/${id}` : `/api/categories/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrCategorys = [...self.props.categories]
                const index = arrCategorys.findIndex(category => category.id === id)
                arrCategorys.splice(index, 1)
                self.props.addUserToState(arrCategorys)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { categories, ignoredColumns, customers } = this.props
        if (categories && categories.length) {
            return categories.map(category => {
                const restoreButton = category.deleted_at
                    ? <RestoreModal id={category.id} entities={categories} updateState={this.props.addUserToState}
                        url={`/api/categories/restore/${category.id}`}/> : null
                const deleteButton = !category.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteCategory} id={category.id}/> : null
                const archiveButton = !category.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteCategory} id={category.id}/> : null

                const editButton = !category.deleted_at ? <EditCategory
                    categories={categories}
                    customers={customers}
                    category={category}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(category).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <CategoryPresenter key={key} customers={customers} edit={editButton}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={category}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(category.id)
                const selectedRow = this.props.viewId === category.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={category.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={category.id} type="checkbox"
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
