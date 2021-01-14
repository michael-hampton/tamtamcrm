import React, { Component } from 'react'
import { DropdownItem, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import GroupModel from '../../models/GroupModel'
import Settings from './Settings'

class EditGroup extends Component {
    constructor (props) {
        super(props)

        this.groupModel = new GroupModel(this.props.group)
        this.initialState = this.groupModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleSettingsChange = this.handleSettingsChange.bind(this)
    }

    handleSettingsChange (event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = value === 'true' ? true : value
        value = value === 'false' ? false : value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value,
            changesMade: true
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
        const formData = {
            account_id: this.state.account_id,
            name: this.state.name,
            settings: this.state.settings
        }

        this.groupModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.groupModel.errors, message: this.groupModel.error_message })
                return
            }

            if (this.props.groups && this.props.action) {
                const index = this.props.groups.findIndex(group => group.id === this.state.id)
                this.props.groups[index] = response
                this.props.action(this.props.groups)
                this.setState({
                    editMode: false,
                    changesMade: false
                })
                this.toggle()
            }
        })
    }

    toggle () {
        if (this.state.modal && this.state.changesMade) {
            if (window.confirm('Your changes have not been saved?')) {
                this.setState({ ...this.initialState })
            }

            return
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const settings = <Settings hasErrorFor={this.hasErrorFor} group={this.state} settings={this.state.settings}
            handleInput={this.handleInput.bind(this)} renderErrorFor={this.renderErrorFor}
            handleSettingsChange={this.handleSettingsChange}
            handleFileChange={this.handleFileChange}/>
        return this.props.modal ? (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>Edit</DropdownItem>
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_group}/>
                    <ModalBody>
                        {settings}
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        ) : settings
    }
}

export default EditGroup
