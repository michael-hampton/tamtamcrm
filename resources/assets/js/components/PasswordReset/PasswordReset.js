import React, { Component } from 'react'
import { Button, Card, CardBody, CardHeader, Form, FormGroup, Input, InputGroup, InputGroupAddon } from 'reactstrap'

import './PasswordReset.css'
import axios from 'axios'

export default class ResetPassword extends Component {
    constructor (props) {
        super(props)

        this.state = {
            code: '',
            email: '',
            error: '',
            success: '',
            password: '',
            codeSent: false,
            confirmed: false,
            confirmPassword: '',
            isConfirming: false,
            isSendingCode: false
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleSendCodeClick = this.handleSendCodeClick.bind(this)
    }

    validateCodeForm () {
        return this.state.email.length > 0
    }

    handleChange (event) {
        this.setState({
            [event.target.id]: event.target.value
        })
    }

    handleSendCodeClick (event) {
        event.preventDefault()

        if (!this.validateCodeForm()) {
            this.setState({ error: 'You must enter an email address' })
            return false
        }

        axios.post('/api/passwordReset/create', {
            email: this.state.email
        })
            .then((response) => {
                if (response.data.success === true) {
                    this.setState({
                        success: 'We have now emailed you a token for you to reset your email',
                        isSendingCode: true
                    })
                }
            })
            .catch((error) => {
                this.setState({
                    isSendingCode: false,
                    error: error.response.data
                })
            })
    }

    renderRequestCodeForm () {
        return (
            <div className="col-md-6 offset-md-3">
                <span className="anchor" id="formResetPassword"/>

                <Card>
                    <CardHeader>
                        <h3 className="mb-0">Password Reset</h3>
                    </CardHeader>

                    <CardBody>
                        <div className="text-center">

                            {this.renderErrorMessage()}
                            {this.renderSuccessMessage()}

                            <h3><i className="fa fa-lock fa-4x"/></h3>
                            <h2 className="text-center">Forgotten Your Password?</h2>
                            <p>You can reset your password here.</p>

                            <Form onSubmit={this.handleSendCodeClick}>
                                <FormGroup>
                                    <InputGroup>
                                        <InputGroupAddon addonType="prepend">
                                            <i className="glyphicon glyphicon-envelope color-blue"/>
                                        </InputGroupAddon>
                                        <Input id="email" name="email" placeholder="email address"
                                            type="email" value={this.state.email} onChange={this.handleChange}/>
                                    </InputGroup>
                                </FormGroup>

                                <FormGroup>
                                    <Button color="primary" type="submit" size="lg" block>Reset Password</Button>
                                </FormGroup>
                            </Form>
                        </div>
                    </CardBody>
                </Card>
            </div>
        )
    }

    renderSuccessMessage () {
        if (!this.state.success.length) {
            return
        }

        return (
            <div className="alert alert-success" role="alert">
                {this.state.success}
            </div>
        )
    }

    renderErrorMessage () {
        if (!this.state.error.length) {
            return
        }

        return (
            <div className="alert alert-danger" role="alert">
                {this.state.error}
            </div>
        )
    }

    render () {
        return (
            <div className="ResetPassword">
                {this.renderRequestCodeForm()}
            </div>
        )
    }
}
