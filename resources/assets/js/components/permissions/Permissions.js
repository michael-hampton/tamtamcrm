/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import EditPermission from './EditPermission'
import AddPermission from './AddPermission'
import { Button } from 'reactstrap'
import DataTable from '../common/DataTable'

export default class Permissions extends Component {
    constructor (props) {
        super(props)

        this.state = {
            permissions: [],
            errors: [],
            error: ''
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
    }

    addUserToState (permissions) {
        this.setState({ permissions: permissions })
    }

    userList () {
        if (this.state.permissions && this.state.permissions.length) {
            return this.state.permissions.map(permission => {
                const columnList = Object.keys(permission).map(key => {
                    return <td key={key}>{permission[key]}</td>
                })
                return <tr key={permission.id}>
                    <td>
                        <Button color="danger" onClick={() => this.deletePermission(permission.id)}>Delete</Button>
                        <EditPermission permission={permission} permissions={this.state.permissions}
                            action={this.addUserToState}/>
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

    deletePermission (id) {
        const self = this
        axios.delete('/api/permissions/' + id)
            .then(function (response) {
                const arrPermissions = [...self.state.permissions]
                const index = arrPermissions.findIndex(permission => permission.id === id)
                arrPermissions.splice(index, 1)
                self.addUserToState(arrPermissions)
            })
            .catch(function (error) {
                self.setState(
                    {
                        error: error.response.data
                    }
                )
            })
    }

    render () {
        const fetchUrl = '/api/permissions/'
        const { error } = this.state

        return (
            <div className="data-table m-md-3 m-0">

                {error && <div className="alert alert-danger" role="alert">
                    {error}
                </div>}

                <AddPermission permissions={this.state.permissions} action={this.addUserToState}/>

                <DataTable
                    disableSorting={['id']}
                    defaultColumn='name'
                    userList={this.userList}
                    fetchUrl={fetchUrl}
                    updateState={this.addUserToState}
                />
            </div>
        )
    }
}
