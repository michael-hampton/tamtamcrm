import React, { Component } from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, UncontrolledTooltip, FormGroup, Input, Label } from 'reactstrap'
import UserRepository from '../../repositories/UserRepository'
import { translations } from '../../utils/_translations'

export default class Google extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            secret: '',
            user_id: ''
        }

        this.toggle = this.toggle.bind(this)
        this.enableGoogle = this.enableGoogle.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    enableGoogle () {
        const userRepository = new UserRepository()

        console.log('props', this.props)

        userRepository.enableGoogle({ user: this.props.user.id, user_id: this.state.user_id, secret: this.state.secret }).then(response => {
            if (!response) {
                this.props.callback(false, response)
                return
            }

            this.props.callback(true, response)
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const button = <Button onClick={this.toggle} outline
        color="primary">{translations.enable_google}</Button>

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="UncontrolledTooltipExample">
                    {translations.enable_google}
                </UncontrolledTooltip>

                {button}

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.enable_two_factor}</ModalHeader>
                    <ModalBody className={theme}>
                        <FormGroup>
                            <Label for="backdrop">{translations.one_time_password}</Label>{' '}
                            <Input type="text" name="user_id" id="user_id" onChange={(e) => {
                                this.setState({ user_id: e.target.value })
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
