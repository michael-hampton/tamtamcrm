import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditBrand from './edit/EditBrand'
import { Input } from 'reactstrap'
import BrandPresenter from '../presenters/BrandPresenter'

export default class BrandItem extends Component {
    constructor (props) {
        super(props)

        this.deleteBrand = this.deleteBrand.bind(this)
    }

    deleteBrand (id, archive = false) {
        const url = archive === true ? `/api/brands/archive/${id}` : `/api/brands/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrBrands = [...self.props.brands]
                const index = arrBrands.findIndex(brand => brand.id === id)
                arrBrands.splice(index, 1)
                self.props.addUserToState(arrBrands)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { brands, ignoredColumns, customers } = this.props
        if (brands && brands.length) {
            return brands.map(brand => {
                const restoreButton = brand.deleted_at
                    ? <RestoreModal id={brand.id} entities={brands} updateState={this.props.addUserToState}
                        url={`/api/brands/restore/${brand.id}`}/> : null
                const deleteButton = !brand.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deleteBrand} id={brand.id}/> : null
                const archiveButton = !brand.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deleteBrand} id={brand.id}/> : null

                const editButton = !brand.deleted_at ? <EditBrand
                    brands={brands}
                    customers={customers}
                    brand={brand}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(brand).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <BrandPresenter key={key} customers={customers} edit={editButton}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={brand}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(brand.id)
                const selectedRow = this.props.viewId === brand.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={brand.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={brand.id} type="checkbox"
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
