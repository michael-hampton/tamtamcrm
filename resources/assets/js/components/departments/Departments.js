/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import EditDepartment from './EditDepartment'
import AddDepartment from './AddDepartment'
import { Button } from 'reactstrap'
import DataTable from '../common/DataTable'

export default class Departments extends Component {
    constructor (props) {
        super(props)

        this.state = {
            departments: [],
            errors: [],
            users: [],
            error: ''
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.ignoredColumns = ['department_manager', 'parent_id']
    }

    componentDidMount () {
        this.getUsers()
    }

    addUserToState (departments) {
        this.setState({ departments: departments })
    }

    userList () {
        if (this.state.departments && this.state.departments.length) {
            return this.state.departments.map(department => {
                const columnList = Object.keys(department).map(key => {
                    if (this.ignoredColumns && !this.ignoredColumns.includes(key)) {
                        return <td key={key}>{department[key]}</td>
                    }
                })
                return <tr key={department.id}>
                    <td>
                        <Button color="danger" onClick={() => this.deleteDepartment(department.id)}>Delete</Button>
                        <EditDepartment
                            users={this.state.users}
                            department={department}
                            departments={this.state.departments}
                            action={this.addUserToState}
                        />
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

    deleteDepartment (id) {
        const self = this
        axios.delete('/api/departments/' + id)
            .then(function (response) {
                const arrDepartments = [...self.state.departments]
                const index = arrDepartments.findIndex(department => department.id === id)
                arrDepartments.splice(index, 1)
                self.addUserToState(arrDepartments)
            })
            .catch(function (error) {
                self.setState(
                    {
                        error: error.response.data
                    }
                )
            })
    }

    getUsers () {
        axios.get('api/users')
            .then((r) => {
                this.setState({
                    users: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    render () {
        const fetchUrl = '/api/departments/'
        const { error } = this.state

        return (
            <div className="data-table m-md-3 m-0">

                {error && <div className="alert alert-danger" role="alert">
                    {error}
                </div>}

                <AddDepartment users={this.state.users} departments={this.state.departments}
                    action={this.addUserToState}/>

                <DataTable
                    disableSorting={['id']}
                    defaultColumn='name'
                    ignore={this.ignoredColumns}
                    userList={this.userList}
                    fetchUrl={fetchUrl}
                    updateState={this.addUserToState}
                />
            </div>
        )
    }
}
