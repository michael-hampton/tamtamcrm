import React from 'react'
import { DropdownItem, Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class EditCaseTemplate extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            name: this.props.template.name,
            description: this.props.template.description,
            send_on: this.props.template.send_on,
            id: this.props.template.id,
            loading: false,
            errors: []
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
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
        axios.put(`/api/case_template/${this.state.id}`, {
            name: this.state.name,
            description: this.state.description,
            send_on: this.state.send_on
        })
            .then((response) => {
                this.toggle()
                const index = this.props.templates.findIndex(template => template.id === this.state.id)
                this.props.templates[index] = response.data
                this.props.action(this.props.templates)
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_template}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_template}/>

                    <ModalBody className={theme}>
                        <Details template={this.state} hasErrorFor={this.hasErrorFor} handleInput={this.handleInput}
                            renderErrorFor={this.renderErrorFor} handleFileChange={this.handleFileChange}/>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditCaseTemplate
