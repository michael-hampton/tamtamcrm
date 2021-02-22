import React, { Component } from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, UncontrolledTooltip } from 'reactstrap'
import UserRepository from '../../repositories/UserRepository'
import { translations } from '../../utils/_translations'

export default class ConfirmEmail extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false
        }

        this.toggle = this.toggle.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    confirmEmail () {
        const userRepository = new UserRepository()

        userRepository.confirmEmail(this.props.user).then(response => {
            if (!response) {
                this.props.callback(false, response)
                return
            }

            this.props.callback(true, response)
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const button = <Button onClick={this.confirmEmail.bind(this)} color="primary">{translations.confirm_email}</Button>

        if (this.props.button_only) {
            return button
        }

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="UncontrolledTooltipExample">
                    {translations.confirm_email}
                </UncontrolledTooltip>

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.confirm_email}</ModalHeader>
                    <ModalBody className={theme}>
                        {button}
                    </ModalBody>

                    <ModalFooter>
                        <Button onClick={this.toggle} color="secondary">{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
