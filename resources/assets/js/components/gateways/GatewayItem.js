import React, { Component } from 'react'
import axios from 'axios'
import RestoreModal from '../common/RestoreModal'
import DeleteModal from '../common/DeleteModal'
import ActionsMenu from '../common/ActionsMenu'
import EditGateway from './edit/EditGateway'
import { Col, Input, Row } from 'reactstrap'
import { ReactSortable } from 'react-sortablejs'

export default class GatewayItem extends Component {
    constructor (props) {
        super(props)

        this.deleteGateway = this.deleteGateway.bind(this)
    }

    deleteGateway (id, archive = false) {
        const url = archive === true ? `/api/gateways/archive/${id}` : `/api/gateways/${id}`
        const self = this
        axios.delete(url)
            .then(function (response) {
                const arrGateways = [...self.props.gateways]
                const index = arrGateways.findIndex(gateway => gateway.id === id)
                arrGateways.splice(index, 1)
                self.props.addUserToState(arrGateways)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { customer_id, group_id, gateways, customers, gateway_ids } = this.props

        const gateway_list = []

        if (gateway_ids.length && gateways.length) {
            gateway_ids.map(item => {
                const gateway = gateways.filter(gateway => parseInt(gateway.id) === parseInt(item))

                gateway_list.push({
                    name: gateway[0].name,
                    id: gateway[0].id,
                    deleted_at: gateway[0].deleted_at
                })
            })
        }

        if (gateways && gateways.length && gateway_ids.length) {
            const gateway_item = gateway_list.length
                ? <ReactSortable
                    tag="ul"
                    className="list-group"
                    list={gateway_list}
                    setList={this.props.setList}
                >
                    {gateway_list.map(item => {
                        let gateway = gateways.filter(gateway => parseInt(gateway.id) === parseInt(item.id))
                        gateway = gateway[0]

                        const restoreButton = gateway.deleted_at
                            ? <RestoreModal id={gateway.id} entities={gateways} updateState={this.props.addUserToState}
                                url={`/api/gateways/restore/${gateway.id}`}/> : null
                        const deleteButton = !gateway.deleted_at
                            ? <DeleteModal archive={false} deleteFunction={this.deleteGateway} id={gateway.id}/> : null
                        const archiveButton = !gateway.deleted_at
                            ? <DeleteModal archive={true} deleteFunction={this.deleteGateway} id={gateway.id}/> : null

                        const editButton = !gateway.deleted_at ? <EditGateway
                            customer_id={customer_id}
                            group_id={group_id}
                            gateways={gateways}
                            customers={customers}
                            gateway={gateway}
                            action={this.props.addUserToState}
                        /> : null

                        const checkboxClass = this.props.showCheckboxes === true ? '' : 'd-none'
                        const isChecked = this.props.bulk.includes(gateway.id)
                        const selectedRow = this.props.viewId === gateway.id ? 'table-row-selected' : ''
                        const actionMenu = this.props.showCheckboxes !== true
                            ? <ActionsMenu edit={editButton} delete={deleteButton} archive={archiveButton}
                                restore={restoreButton}/> : null
                        const dark = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' ? 'list-group-item-dark' : ''

                        return <li key={item.id}
                            className={`list-group-item d-flex justify-content-between align-items-center ${dark}`}>
                            <div className="d-flex justify-content-between">
                                <Input style={{ marginLeft: '8px' }} checked={isChecked} className={checkboxClass}
                                    value={gateway.id} type="checkbox"
                                    onChange={this.props.onChangeBulk}/>
                                {actionMenu}

                                <h4 style={{ marginLeft: '40px' }}
                                    onClick={() => this.props.toggleViewedEntity(gateway, gateway.name)}>{item.name} </h4>
                            </div>

                            {!!this.props.isFiltered &&
                            <span style={{ cursor: 'pointer' }}
                                onClick={() => this.props.removeFromList(gateway.id)}>Remove</span>
                            }

                        </li>
                    })}
                </ReactSortable> : null

            return <Row className="mt-2 mb-2">
                <Col sm={8}>
                    {gateway_item}
                </Col>
            </Row>
        } else {
            return <tr>
                <td className="text-center">No Records Found.</td>
            </tr>
        }
    }
}
