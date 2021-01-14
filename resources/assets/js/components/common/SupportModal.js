import React, { Component } from 'react'
import {
    Button,
    Form,
    FormGroup,
    Input,
    Label,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    UncontrolledTooltip
} from 'reactstrap'
import axios from 'axios'
import SuccessMessage from './SucessMessage'
import ErrorMessage from './ErrorMessage'
import { translations } from '../utils/_translations'

export default class SupportModal extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            check: false,
            errors: [],
            showSuccessMessage: false,
            showErrorMessage: false,
            message: ''
        }

        this.toggle = this.toggle.bind(this)
        this.sendMessage = this.sendMessage.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    sendMessage () {
        axios.post('/api/support/messages/send', { message: this.state.message, send_logs: this.state.check })
            .then(function (response) {

            })
            .catch(function (error) {
                alert(error)
                console.log(error)
            })
    }

    handleChange (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Your message has been sent successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true ? <ErrorMessage
            message="Your message could not be sent"/> : null
        const color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
            ? '#fff' : '#000'

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="aboutTooltip">
                    {translations.contact_us}
                </UncontrolledTooltip>

                <i id="aboutTooltip" onClick={this.toggle}
                    style={{ color: color, fontSize: '20px', cursor: 'pointer' }}
                    className="fa fa-envelope"/>

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.contact_us}</ModalHeader>
                    <ModalBody className={theme}>
                        {successMessage}
                        {errorMessage}
                        <Form>
                            <FormGroup>
                                <Label for="exampleEmail">{translations.message}</Label>
                                <Input type="textarea" onChange={this.handleChange} name="message" id="message"
                                    placeholder="Message"/>
                            </FormGroup>
                            <FormGroup check>
                                <Label check>
                                    <Input name="check" type="checkbox" checked={this.state.check}
                                        onChange={(e) => {
                                            this.handleChange({
                                                target: {
                                                    name: e.target.name,
                                                    value: e.target.checked
                                                }
                                            })
                                        }}/>
                                    Include recent errors from the logs
                                </Label>
                            </FormGroup>
                        </Form>
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={this.sendMessage}
                            color="primary">{translations.send}</Button>
                        <Button onClick={this.toggle} color="secondary">{translations.cancel}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
