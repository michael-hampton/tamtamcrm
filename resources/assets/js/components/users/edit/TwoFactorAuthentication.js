import React, { Component } from 'react'
import {
    Button,
    FormGroup,
    Input,
    Label,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    UncontrolledTooltip
} from 'reactstrap'
import UserRepository from '../../repositories/UserRepository'
import { translations } from '../../utils/_translations'
import QRCode from 'react-qr-code'

export default class TwoFactorAuthentication extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            secret: '',
            one_time_password: '',
            qr_code: '',
            user_id: null
        }

        this.toggle = this.toggle.bind(this)
        this.enableTwoFactor = this.enableTwoFactor.bind(this)
        this.setupTwoFactor = this.setupTwoFactor.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.user.id && props.user.id !== state.user_id) {
            return { user_id: props.user.id }
        }

        return null
    }

    componentDidMount () {
        if (this.props.user.id) {
            this.setupTwoFactor(this.props.user.id)
        }
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.user.id && this.props.user.id !== prevProps.user.id) {
            this.setupTwoFactor(this.props.user.id)
        }
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    setupTwoFactor () {
        const userRepository = new UserRepository()

        console.log('props', this.props)

        userRepository.setupTwoFactorAuthentication(this.props.user.id).then(response => {
            if (!response) {
                // this.props.callback(false, response)
                return
            }

            this.setState({ secret: response.secret, qr_code: response.qr_code })

            console.log('response', response)
        })
    }

    enableTwoFactor () {
        const userRepository = new UserRepository()

        console.log('props', this.props)

        userRepository.enableTwoFactorAuthentication({
            user: this.props.user.id,
            one_time_password: this.state.one_time_password,
            secret: this.state.secret
        }).then(response => {
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
                        {this.state.qr_code.length && this.state.secret.length &&
                        <div className="m-4 text-center">
                            <QRCode value={this.state.qr_code}/>
                        </div>
                        }

                        <FormGroup>
                            <Label for="backdrop">{translations.one_time_password}</Label>{' '}
                            <Input type="text" name="one_time_password" id="one_time_password" onChange={(e) => {
                                this.setState({ one_time_password: e.target.value })
                            }}/>
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
