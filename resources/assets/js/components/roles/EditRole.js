import React from 'react'
import {
    Button,
    Input,
    InputGroup,
    InputGroupAddon,
    InputGroupText,
    Label,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader
} from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'

class EditRole extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            loading: false,
            errors: [],
            name: this.props.role.name,
            description: this.props.role.description,
            permissions: [],
            attachedPermissions: [],
            selectedPermissions: [],
            role: [],
            message: ''
        }
        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.buildPermissionList = this.buildPermissionList.bind(this)
    }

    componentDidMount () {
        this.getRole()
    }

    handleInput (e) {
        this.setState({ [e.target.name]: e.target.value })
    }

    handleMultiSelect (e) {
        this.setState({ attachedPermissions: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        axios.put(`/api/roles/${this.state.role.id}`, {
            name: this.state.name,
            description: this.state.description,
            permissions: this.state.attachedPermissions
        })
            .then((response) => {
                this.toggle()
                const index = this.props.roles.findIndex(role => role.id === this.props.role.id)
                this.props.roles[index].name = this.state.name
                this.props.roles[index].description = this.state.description
                this.props.action(this.props.roles)
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.setState({
                        errors: error.response.data.errors
                    })
                } else {
                    this.setState({ message: error.response.data })
                }
            })
    }

    getRole () {
        axios.get(`/api/roles/${this.props.role.id}`)
            .then((r) => {
                this.setState({
                    permissions: r.data.permissions,
                    attachedPermissions: r.data.attachedPermissions,
                    role: r.data.role
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: [],
            message: ''
        })
    }

    buildPermissionList () {
        let permissionsList = null
        if (!this.state.permissions.length) {
            permissionsList = <option value="">Loading...</option>
        } else {
            permissionsList = this.state.permissions.map((permission, index) => {
                return (
                    <option key={index} value={permission.id}>{permission.name}</option>
                )
            })
        }

        return permissionsList
    }

    render () {
        const permissionsList = this.buildPermissionList()
        const { message } = this.state

        return (
            <React.Fragment>
                <Button className="ml-2" color="success" onClick={this.toggle}>{translations.edit_role}</Button>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>
                        {translations.edit_role}
                    </ModalHeader>
                    <ModalBody>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <Label>{translations.name}</Label>
                        <InputGroup className="mb-3">
                            <InputGroupAddon addonType="prepend">
                                <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                            </InputGroupAddon>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                                placeholder="Name"
                                type="text" name="name"
                                value={this.state.name}
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </InputGroup>

                        <Label>{translations.description}</Label>
                        <InputGroup className="mb-3">
                            <InputGroupAddon addonType="prepend">
                                <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                            </InputGroupAddon>
                            <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''}
                                placeholder="Description"
                                type="text"
                                name="description"
                                value={this.state.description}
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('description')}
                        </InputGroup>

                        <Label>Assign Permissions</Label>
                        <InputGroup className="mb-3">
                            <InputGroupAddon addonType="prepend">
                                <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                            </InputGroupAddon>
                            <Input value={this.state.attachedPermissions} onChange={this.handleMultiSelect}
                                type="select" multiple>
                                {permissionsList}
                            </Input>
                        </InputGroup>

                    </ModalBody>

                    <ModalFooter>
                        <Button color="primary" onClick={this.handleClick.bind(this)}>{translations.save}</Button>
                        <Button color="secondary" onClick={this.toggle}>{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditRole
