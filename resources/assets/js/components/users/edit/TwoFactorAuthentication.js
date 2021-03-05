import React, { Component } from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, UncontrolledTooltip, FormGroup, Input, Label } from 'reactstrap'
import UserRepository from '../../repositories/UserRepository'
import { translations } from '../../utils/_translations'

export default class TwoFactorAuthentication extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            secret: '',
            one_time_password: ''
        }

        this.toggle = this.toggle.bind(this)
        this.enableTwoFactor = this.enableTwoFactor.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    enableTwoFactor () {
        const userRepository = new UserRepository()

        console.log('props', this.props)

        userRepository.enableTwoFactorAuthentication({ user: this.props.user.id, one_time_password: this.state.one_time_password, secret: this.state.secret }).then(response => {
            if (!response) {
                this.props.callback(false, response)
                return
            }

            this.props.callback(true, response)
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const button = <Button onClick={(e) => {
            if (!this.props.user.two_factor_authentication_enabled === true) {
                if (!this.props.user.phone_number) {
                    alert(translations.two_factor_no_phone_entered)
                    return false
                }

                this.setState({ two_factor_authentication_enabled: true })
                this.toggle()
            } else {
                this.setState({ two_factor_authentication_enabled: false })
            }
        }} outline
        color="primary">{this.props.user.two_factor_authentication_enabled === true ? translations.disable_two_factor : translations.enable_two_factor}</Button>

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="UncontrolledTooltipExample">
                    {translations.enable_two_factor}
                </UncontrolledTooltip>

                {button}

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.enable_two_factor}</ModalHeader>
                    <ModalBody className={theme}>
                        <FormGroup>
                            <Label for="backdrop">{translations.one_time_password}</Label>{' '}
                            <Input type="text" name="one_time_password" id="one_time_password" onChange={(e) => {
                                this.setState({ one_time_password: e.target.value })
                            }} />
                        </FormGroup>

                        <FormGroup>
                            <Label for="backdrop">{translations.secret}</Label>{' '}
                            <Input type="text" name="secret" id="secret" onChange={(e) => {
                                this.setState({ secret: e.target.value })
                            }} />
                        </FormGroup>
                    </ModalBody>

                    <ModalFooter>
                        <Button color="primary" onClick={this.enableTwoFactor}>{translations.save}</Button>{' '}
                        <Button onClick={this.toggle} color="secondary">{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
