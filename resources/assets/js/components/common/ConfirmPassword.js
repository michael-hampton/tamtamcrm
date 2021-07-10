import React, { Component } from 'react'
import { DropdownItem, FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { translations } from '../utils/_translations'
import DefaultModalHeader from './ModalHeader'
import DefaultModalFooter from './ModalFooter'

export default class ConfirmPassword extends Component {
    constructor (props) {
        super(props)
        this.state = {
            password: '',
            confirm_text: '',
            errors: [],
            message: '',
            modal: false
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('taxForm', JSON.stringify(this.state)))
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
        if (!this.state.password.length && !this.state.confirm_text.length) {
            const message = this.props.text_input ? 'Please enter the text' : 'Please enter a password'
            alert(message)
            return false
        }

        this.toggle()
        localStorage.setItem('password_last_set', new Date())
        this.props.callback(this.props.id, this.state.password)
    }

    toggle () {
        const diff = Math.abs(new Date(localStorage.getItem('password_last_set')) - new Date())
        const minutes = Math.floor((diff / 1000) / 60)

        if (minutes <= 30) {
            this.props.callback(this.props.id, '')
            return true
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const icon = this.props.icon
            ? <i style={this.props.icon_style || null} className={`fa ${this.props.icon} mr-2`}/> : null
        const button = this.props.dropdown
            ? <DropdownItem onClick={this.toggle}>{icon}{this.props.button_label}
            </DropdownItem> : <button className={`btn ${this.props.button_color}`}
                onClick={this.toggle}>{icon}{this.props.button_label}</button>
        const input = this.props.text_input === true ? <FormGroup className="mb-3">
            <Label>{translations.confirm_text_for_delete}</Label>
            <Input className={this.hasErrorFor('confirm_text') ? 'is-invalid' : ''} type="text"
                name="confirm_text"
                value={this.state.confirm_text} onChange={this.handleInput.bind(this)}/>
            {this.renderErrorFor('confirm_text')}
        </FormGroup> : <FormGroup className="mb-3">
            <Label>{translations.confirm_password}</Label>
            <Input className={this.hasErrorFor('password') ? 'is-invalid' : ''} type="password"
                name="password"
                value={this.state.password} onChange={this.handleInput.bind(this)}/>
            {this.renderErrorFor('password')}
        </FormGroup>

        return (
            <React.Fragment>
                {button}
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.confirm_password_title}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        {this.props.text && <p>{this.props.text}</p>}

                        {input}

                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
