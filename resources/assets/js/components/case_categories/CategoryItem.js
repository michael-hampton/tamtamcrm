import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditCategory from './edit/EditCategory'
import { Input, ListGroupItem } from 'reactstrap'
import CategoryPresenter from '../presenters/CategoryPresenter'

export default class CategoryItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            width: window.innerWidth
        }

        this.deleteCategory = this.deleteCategory.bind(this)
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

    deleteCategory (id, archive = false) {
        const url = archive === true ? `/api/case-categories/archive/${id}` : `/api/categories/${id}`
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
            return categories.map((category, index) => {
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
                    return <CategoryPresenter key={key} customers={customers}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={category} edit={editButton}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(category.id)
                const selectedRow = this.props.viewId === category.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu show_list={this.props.show_list} edit={editButton} delete={deleteButton}
                        archive={archiveButton}
                        restore={restoreButton}/> : null

                const is_mobile = this.state.width <= 768
                const list_class = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
                    ? 'list-group-item-dark' : ''

                if (!this.props.show_list) {
                    return <tr className={selectedRow} key={category.id}>
                        <td>
                            <Input checked={isChecked} className={checkboxClass} value={category.id} type="checkbox"
                                onChange={this.props.onChangeBulk}/>
                            {actionMenu}
                        </td>
                        {columnList}
                    </tr>
                }

                return is_mobile || this.props.force_mobile ? <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={category.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(category, category.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{category.name}</h5>
                        </div>
                    </ListGroupItem>
                </div> : <div className={`d-flex d-inline ${list_class}`}>
                    <div className="list-action">
                        {!!this.props.onChangeBulk &&
                        <Input checked={isChecked} className={checkboxClass} value={category.id} type="checkbox"
                            onChange={this.props.onChangeBulk}/>
                        }
                        {actionMenu}
                    </div>

                    <ListGroupItem
                        onClick={() => this.props.toggleViewedEntity(category, category.name, editButton)}
                        key={index}
                        className={`border-top-0 list-group-item-action flex-column align-items-start ${list_class}`}>
                        <div className="d-flex w-100 justify-content-between">
                            <h5 className="mb-1">{category.name}</h5>
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
