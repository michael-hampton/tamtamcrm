import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input, Label } from 'reactstrap'

export default class RoleDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: []
        }

        this.getRoles = this.getRoles.bind(this)
    }

    componentDidMount () {
        if (!this.props.roles || !this.props.roles.length) {
            this.getRoles()
        } else {
            this.setState({ roles: this.props.roles })
        }
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback d-block'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
    }

    getRoles () {
        axios.get('/api/roles')
            .then((r) => {
                this.setState({
                    roles: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    multiSelect (roleList, name) {
        return (
            <FormGroup>
                <Label>Select Roles for user</Label>
                <Input value={this.props.role} onChange={this.props.handleInputChanges} type="select"
                    name={name} id={name}
                    multiple="multiple"
                >
                    {roleList}
                </Input>
                {this.renderErrorFor('role')}
            </FormGroup>
        )
    }

    singleSelect (roleList, name) {
        return (
            <FormGroup>
                <Input value={this.props.role} onChange={this.props.handleInputChanges} type="select"
                    name={name} id={name}
                >
                    <option value="">Select Role</option>
                    {roleList}
                </Input>
                {this.renderErrorFor('role')}
            </FormGroup>
        )
    }

    render () {
        let roleList = null
        if (!this.state.roles.length) {
            roleList = <option value="">Loading...</option>
        } else {
            roleList = this.state.roles.map((role, index) => (
                <option key={index} value={role.id}>{role.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'role'

        if (this.props.multiple && this.props.multiple === true) {
            return this.multiSelect(roleList, name)
        }

        return this.singleSelect(roleList, name)
    }
}
