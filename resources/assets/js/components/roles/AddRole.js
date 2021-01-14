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
import Select from 'react-select'
import { translations } from '../utils/_translations'

class AddRole extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            name: '',
            description: '',
            loading: false,
            errors: [],
            attachedPermissions: [],
            selectedPermissions: [],
            permissions: [],
            message: ''
        }
        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.buildPermissionList = this.buildPermissionList.bind(this)
    }

    componentDidMount () {
        this.getPermissions()
    }

    handleMultiSelect (e) {
        this.setState({ attachedPermissions: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
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

    getPermissions () {
        axios.get('/api/permissions')
            .then((r) => {
                this.setState({
                    permissions: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    handleClick () {
        axios.post('/api/roles', {
            name: this.state.name,
            description: this.state.description,
            permissions: this.state.attachedPermissions
        })
            .then((response) => {
                const newUser = response.data
                this.props.roles.push(newUser)
                this.props.action(this.props.roles)
                this.setState({
                    name: null,
                    description: null
                })
                this.toggle()
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

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: [],
            message: ''
        })
    }

    buildPermissionList () {
        let permissionsList = null
        console.log('state', this.state)
        if (!this.state.permissions.length) {
            permissionsList = <option value="">Loading...</option>
        } else {
            permissionsList = this.state.permissions.map((permission, index) => {
                const selected = this.state.attachedPermissions.indexOf(permission.id) > -1 ? 'selected' : ''
                return (
                    <option selected={selected} key={index} value={permission.id}>{permission.name}</option>
                )
            })
        }

        return permissionsList
    }

    render () {
        const { message } = this.state

        return (
            <React.Fragment>
                <Button className="pull-right" color="success" onClick={this.toggle}>{translations.add_role}</Button>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>
                        {translations.add_role}
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
                                type="text"
                                name="name"
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
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('description')}
                        </InputGroup>

                        <Label>Assign Permissions</Label>
                        <InputGroup className="mb-3">
                            <InputGroupAddon addonType="prepend">
                                <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                            </InputGroupAddon>

                            <Select
                                onChange={options => {
                                    if (Array.isArray(options)) {
                                        this.setState({ attachedPermissions: options.map(opt => opt.id) })
                                    }
                                }}
                                getOptionLabel={option => option.name}
                                getOptionValue={option => option.id}
                                isMulti
                                name="permissions"
                                options={this.state.permissions}
                                className="basic-multi-select"
                                classNamePrefix="select"
                            />

                            {/* <Input onChange={this.handleMultiSelect} type="select" multiple> */}
                            {/*    {permissionsList} */}
                            {/* </Input> */}
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

export default AddRole
