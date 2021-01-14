/* eslint-disable no-unused-vars */
import React from 'react'
import { Button, Input, InputGroup, InputGroupAddon, InputGroupText, Label, Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import DefaultModalHeader from '../common/ModalHeader'
import DefaultModalFooter from '../common/ModalFooter'

class EditDepartment extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            loading: false,
            errors: [],
            name: this.props.department.name,
            department_id: this.props.department.id,
            parent: this.props.department.parent_id,
            department_manager: this.props.department.department_manager,
            permissions: [],
            attachedPermissions: [],
            selectedPermissions: [],
            department: []
        }

        this.initialState = this.state
        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.buildUserOptions = this.buildUserOptions.bind(this)
        this.buildParentOptions = this.buildParentOptions.bind(this)
    }

    handleInput (e) {
        this.setState({ [e.target.name]: e.target.value })
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

    buildUserOptions () {
        let userContent
        if (!this.props.users.length) {
            userContent = <option value="">Loading...</option>
        } else {
            userContent = this.props.users.map((user, index) => (
                <option key={index} value={user.id}>{user.first_name + ' ' + user.last_name}</option>
            ))
        }

        return (
            <React.Fragment>
                <Label>Department Manager</Label>
                <InputGroup className="mb-3">
                    <InputGroupAddon addonType="prepend">
                        <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                    </InputGroupAddon>
                    <Input className={this.hasErrorFor('department_manager') ? 'is-invalid' : ''}
                        type="select"
                        value={this.state.department_manager}
                        name="department_manager"
                        id="department_manager"
                        onChange={this.handleInput.bind(this)}>
                        <option value="">Choose Department Manager</option>
                        {userContent}
                    </Input>
                    {this.renderErrorFor('department_manager')}
                </InputGroup>
            </React.Fragment>
        )
    }

    buildParentOptions () {
        let departmentList
        if (!this.props.departments.length) {
            departmentList = <option value="">Loading...</option>
        } else {
            departmentList = this.props.departments.map((department, index) => (
                <option key={index} value={department.id}>{department.name}</option>
            ))
        }

        return (
            <React.Fragment>
                <Label>Parent</Label>
                <InputGroup className="mb-3">
                    <InputGroupAddon addonType="prepend">
                        <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                    </InputGroupAddon>
                    <Input className={this.hasErrorFor('parent') ? 'is-invalid' : ''}
                        value={this.state.parent}
                        type="select"
                        name="parent"
                        onChange={this.handleInput.bind(this)}>
                        <option value="">Select Parent</option>
                        {departmentList}
                    </Input>
                    {this.renderErrorFor('parent')}
                </InputGroup>
            </React.Fragment>
        )
    }

    handleClick () {
        axios.put(`/api/departments/${this.state.department_id}`, {
            name: this.state.name,
            department_manager: this.state.department_manager,
            parent: this.state.parent
        })
            .then((response) => {
                this.initialState = this.state
                const index = this.props.departments.findIndex(department => department.id === this.props.department.id)
                this.props.departments[index].name = this.state.name
                this.props.departments[index].department_manager = this.state.department_manager
                this.props.action(this.props.departments)
                this.toggle()
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    toggle () {
        if (this.state.modal) {
            this.setState({ ...this.initialState })
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const userOptions = this.buildUserOptions()
        const parentDropdown = this.buildParentOptions()

        return (
            <React.Fragment>
                <Button className="ml-2" color="success" onClick={this.toggle}>{translations.edit_department}</Button>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_department}/>

                    <ModalBody>
                        <Label>{translations.name}</Label>
                        <InputGroup className="mb-3">
                            <InputGroupAddon addonType="prepend">
                                <InputGroupText><i className="fa fa-user-o"/></InputGroupText>
                            </InputGroupAddon>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                                placeholder="Name"
                                type="text"
                                name="name"
                                value={this.state.name}
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </InputGroup>

                        {parentDropdown}
                        {userOptions}

                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditDepartment
