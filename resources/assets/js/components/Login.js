import React, { Component } from 'react'
import axios from 'axios'

import {
    Button,
    Card,
    CardBody,
    CardGroup,
    Col,
    Container,
    Form,
    Input,
    InputGroup,
    InputGroupAddon,
    InputGroupText,
    Row
} from 'reactstrap'
import queryString from 'query-string'
import { icons } from './utils/_icons'
import { translations } from './utils/_translations'
import ConfirmPassword from './common/ConfirmPassword'
import ForgotPassword from './ForgotPassword'

class Login extends Component {
    constructor (props) {
        super(props)

        this.state = {
            loading: false,
            email: '',
            password: '',
            error: '',
            confirmed: queryString.parse(this.props.location.search).confirmed || false,
            should_confirm_password: false
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.onSocialClick = this.onSocialClick.bind(this)
        this.dismissError = this.dismissError.bind(this)
    }

    dismissError () {
        this.setState({ error: '' })
    }

    onSocialClick (param, e) {
        window.location.assign(`auth/${param}`)
        this.setState({
            loading: true,
            isLoading: true
        })
    }

    handleSubmit (evt) {
        evt.preventDefault()

        if (!this.state.email) {
            return this.setState({ error: 'Email is required' })
        }

        if (!this.state.password) {
            return this.setState({ error: 'Password is required' })
        }

        this.setState({ loading: true })

        axios.post('/api/login', {
            email: this.state.email,
            password: this.state.password
        })
            .then((response) => {
                if (response.data.success === true) {
                    console.log('data', response.data)

                    const userData = {
                        name: response.data.data.name,
                        id: response.data.data.id,
                        email: response.data.data.email,
                        account_id: response.data.data.account_id,
                        auth_token: response.data.data.auth_token,
                        timestamp: new Date().toString()
                    }

                    const appState = {
                        isLoggedIn: true,
                        user: userData,
                        accounts: response.data.data.accounts
                    }

                    window.sessionStorage.setItem('authenticated', true)

                    var d1 = new Date()
                    var d2 = new Date(d1)
                    d2.setMinutes(d1.getMinutes() + 154.8)

                    // save app state with user date in local storage
                    localStorage.setItem('allowed_permissions', JSON.stringify(response.data.data.allowed_permissions))
                    localStorage.appState = JSON.stringify(appState)
                    localStorage.setItem('account_id', response.data.data.account_id)
                    localStorage.setItem('currencies', JSON.stringify(response.data.data.currencies))
                    localStorage.setItem('languages', JSON.stringify(response.data.data.languages))
                    localStorage.setItem('custom_fields', JSON.stringify(response.data.data.custom_fields))
                    localStorage.setItem('countries', JSON.stringify(response.data.data.countries))
                    localStorage.setItem('payment_types', JSON.stringify(response.data.data.payment_types))
                    localStorage.setItem('gateways', JSON.stringify(response.data.data.gateways))
                    localStorage.setItem('tax_rates', JSON.stringify(response.data.data.tax_rates))
                    localStorage.setItem('users', JSON.stringify(response.data.data.users))
                    localStorage.setItem('number_of_accounts', response.data.data.number_of_accounts)
                    localStorage.setItem('industries', JSON.stringify(response.data.data.industries))
                    localStorage.setItem('plan', JSON.stringify(response.data.data.plan))
                    localStorage.setItem('access_token', userData.auth_token)
                    localStorage.setItem('expires', d2)
                    localStorage.setItem('last_login', d1)
                    localStorage.setItem('require_login', response.data.data.require_login)

                    this.setState({
                        isLoggedIn: appState.isLoggedIn,
                        user: appState.user
                    })
                    window.location.href = '/'
                } else {
                    return this.setState({ error: 'Unable to log in' })
                }
            })
    }

    handleChange (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    render () {
        const social_login_button = this.state.should_confirm_password
            ? <ConfirmPassword id="google" callback={this.onSocialClick.bind(this, 'google')}
                button_color="btn-primary" button_label="Login With Google"/>
            : <a onClick={this.onSocialClick.bind(this, 'google')}
                style={{
                    marginTop: '0px !important',
                    background: 'green',
                    color: '#ffffff',
                    padding: '5px',
                    borderRadius: '7px'
                }}
                className="ml-2 btn-google">
                <strong>Login With Google</strong>
            </a>
        return (
            <div className="app flex-row align-items-center">
                <Container>
                    <Row className="justify-content-center">
                        <Col md="8">
                            <CardGroup>
                                <Card className="p-4">
                                    <CardBody>
                                        <Form onSubmit={this.handleSubmit}>

                                            {
                                                this.state.error &&
                                                <div className="alert alert-danger alert-dismissible" data-test="error"
                                                    onClick={this.dismissError}>
                                                    <button type="button" className="close" aria-label="Close"><span
                                                        aria-hidden="true">×</span></button>
                                                    {this.state.error}
                                                </div>
                                            }

                                            <h1>{translations.login}</h1>
                                            <p className="text-muted">Sign In to your account</p>
                                            <InputGroup className="mb-3">
                                                <InputGroupAddon addonType="prepend">
                                                    <InputGroupText>
                                                        <i className="icon-user"/>
                                                    </InputGroupText>
                                                </InputGroupAddon>
                                                <Input type="text" name="email" placeholder={translations.email}
                                                    autoComplete="email" onChange={this.handleChange.bind(this)}/>
                                            </InputGroup>
                                            <InputGroup className="mb-4">
                                                <InputGroupAddon addonType="prepend">
                                                    <InputGroupText>
                                                        <i className="icon-lock"/>
                                                    </InputGroupText>
                                                </InputGroupAddon>
                                                <Input type="password" name="password"
                                                    placeholder={translations.password}
                                                    autoComplete="current-password"
                                                    onChange={this.handleChange.bind(this)}/>
                                            </InputGroup>
                                            <Row>
                                                <div className="col-12 d-flex justify-content-between">
                                                    <div>
                                                        <Button disabled={this.state.loading} type="submit"
                                                            color="primary"
                                                            className="px-4">{translations.login}</Button>
                                                        {this.state.loading &&
                                                        <span style={{ fontSize: '36px' }}
                                                            className={`fa ${icons.spinner}`}/>
                                                        }
                                                    </div>
                                                    {!this.state.loading && social_login_button}
                                                    <ForgotPassword/>
                                                </div>
                                            </Row>
                                        </Form>
                                    </CardBody>
                                </Card>
                            </CardGroup>
                        </Col>
                    </Row>
                </Container>
            </div>
        )
    }
}

export default Login
