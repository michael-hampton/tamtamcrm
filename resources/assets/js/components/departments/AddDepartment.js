import React from 'react'
import { Button, Input, InputGroup, InputGroupAddon, InputGroupText, Label, Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import DefaultModalHeader from '../common/ModalHeader'
import DefaultModalFooter from '../common/ModalFooter'

class AddDepartment extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            name: '',
            department_manager: '',
            parent: 0,
            loading: false,
            errors: [],
            message: ''
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.buildUserOptions = this.buildUserOptions.bind(this)
        this.buildParentOptions = this.buildParentOptions.bind(this)
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

    handleClick () {
        axios.post('/api/departments', {
            name: this.state.name,
            department_manager: this.state.department_manager,
            parent: this.state.parent
        })
            .then((response) => {
                this.toggle()
                const newUser = response.data
                this.props.departments.push(newUser)
                this.props.action(this.props.departments)
                this.setState({
                    name: null,
                    department_manager: null
                })
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

    render () {
        const userOptions = this.buildUserOptions()
        const parentDropdown = this.buildParentOptions()
        const { message } = this.state

        return (
            <React.Fragment>
                <Button className="pull-right" color="success"
                    onClick={this.toggle}>{translations.add_department}</Button>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_department}/>

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

export default AddDepartment
