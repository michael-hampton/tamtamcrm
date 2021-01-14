import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditPromocode from './edit/EditPromocode'
import { Input } from 'reactstrap'
import PromocodePresenter from '../presenters/PromocodePresenter'

export default class PromocodeItem extends Component {
    constructor (props) {
        super(props)

        this.deletePromocode = this.deletePromocode.bind(this)
    }

    deletePromocode (id, archive = false) {
        const url = archive === true ? `/api/promocodes/archive/${id}` : `/api/promocodes/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrPromocodes = [...self.props.promocodes]
                const index = arrPromocodes.findIndex(promocode => promocode.id === id)
                arrPromocodes.splice(index, 1)
                self.props.addUserToState(arrPromocodes)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { promocodes, ignoredColumns } = this.props
        if (promocodes && promocodes.length) {
            return promocodes.map(promocode => {
                const restoreButton = promocode.deleted_at
                    ? <RestoreModal id={promocode.id} entities={promocodes} updateState={this.props.addUserToState}
                        url={`/api/promocodes/restore/${promocode.id}`}/> : null
                const deleteButton = !promocode.deleted_at
                    ? <DeleteModal archive={false} deleteFunction={this.deletePromocode} id={promocode.id}/> : null
                const archiveButton = !promocode.deleted_at
                    ? <DeleteModal archive={true} deleteFunction={this.deletePromocode} id={promocode.id}/> : null

                const editButton = !promocode.deleted_at ? <EditPromocode
                    promocodes={promocodes}
                    promocode={promocode}
                    action={this.props.addUserToState}
                /> : null

                const columnList = Object.keys(promocode).filter(key => {
                    return ignoredColumns && !ignoredColumns.includes(key)
                }).map(key => {
                    return <PromocodePresenter key={key} customers={this.props.customers} edit={editButton}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        field={key} entity={promocode}/>
                })

                const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                const isChecked = this.props.bulk.includes(promocode.id)
                const selectedRow = this.props.viewId === promocode.id ? 'table-row-selected' : ''
                const actionMenu = this.props.showCheckboxes !== true
                    ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                        restore={restoreButton}/> : null

                return <tr className={selectedRow} key={promocode.id}>
                    <td>
                        <Input checked={isChecked} className={checkboxClass} value={promocode.id} type="checkbox"
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
