/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import EditRole from './EditRole'
import AddRole from './AddRole'
import { Alert, Button } from 'reactstrap'
import DataTable from '../common/DataTable'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'

export default class Roles extends Component {
    constructor (props) {
        super(props)

        this.state = {
            roles: [],
            errors: [],
            error: ''
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
    }

    addUserToState (roles) {
        this.setState({ roles: roles })
    }

    userList () {
        if (this.state.roles && this.state.roles.length) {
            return this.state.roles.map(role => {
                const columnList = Object.keys(role).map(key => {
                    return <td key={key}>{role[key]}</td>
                })
                return <tr key={role.id}>
                    <td>
                        <Button color="danger" onClick={() => this.deleteRole(role.id)}>Delete</Button>
                        <EditRole role={role} roles={this.state.roles} action={this.addUserToState}/>
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

    deleteRole (id) {
        const self = this
        axios.delete('/api/roles/' + id)
            .then(function (response) {
                const arrRoles = [...self.state.roles]
                const index = arrRoles.findIndex(role => role.id === id)
                arrRoles.splice(index, 1)
                self.addUserToState(arrRoles)
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
        const fetchUrl = '/api/roles/'
        const { error } = this.state

        return (
            <div className="data-table m-md-3 m-0">

                {error &&
                <Snackbar open={this.state.error} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                    <Alert severity="danger">
                        {translations.unexpected_error}
                    </Alert>
                </Snackbar>
                }

                <AddRole roles={this.state.roles} action={this.addUserToState}/>

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
