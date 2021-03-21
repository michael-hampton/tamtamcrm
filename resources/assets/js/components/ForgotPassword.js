import React, { Component } from 'react'
import { FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { toast, ToastContainer } from 'react-toastify'
import UserRepository from './repositories/UserRepository'
import DefaultModalFooter from './common/ModalFooter'
import { translations } from './utils/_translations'
import DefaultModalHeader from './common/ModalHeader'

export default class ForgotPassword extends Component {
    constructor (props) {
        super(props)
        this.state = {
            password: '',
            email: '',
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
        if (!this.state.email.length) {
            const message = 'Please enter your email address'
            alert(message)
            return false
        }

        const userRepository = new UserRepository()

        userRepository.forgotPassword(this.state.email).then(response => {
            if (!response) {
                toast.error(translations.forgot_password_link_failed, {
                    position: 'top-center',
                    autoClose: 5000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })
                return
            }

            toast.success(translations.forgot_password_link_sent, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            setTimeout(
                () => this.setState({ modal: false }),
                5000
            )
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
                <button type="button" className="btn btn-link"
                    onClick={this.toggle}>{translations.forgot_password}</button>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.forgot_password}/>

                    <ModalBody className={theme}>

                        <ToastContainer
                            position="top-center"
                            autoClose={5000}
                            hideProgressBar={false}
                            newestOnTop={false}
                            closeOnClick
                            rtl={false}
                            pauseOnFocusLoss
                            draggable
                            pauseOnHover
                        />

                        <FormGroup className="mb-3">
                            <Label>{translations.email}</Label>
                            <Input className={this.hasErrorFor('email') ? 'is-invalid' : ''} type="text"
                                name="email"
                                value={this.state.email} onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('email')}
                        </FormGroup>

                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
