import React, { Component } from 'react'
import { Button, Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import FormBuilder from './FormBuilder'
import ColorPicker from '../common/ColorPicker'
import Header from './Header'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'

export default class DeviceSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            success_message: translations.settings_saved,
            id: localStorage.getItem('account_id'),
            settings: {
                dark_theme: !!(!Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')),
                number_of_rows: localStorage.getItem('number_of_rows') || 10
            },
            success: false,
            error: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleHeaderColor = this.handleHeaderColor.bind(this)
        this.handleFooterColor = this.handleFooterColor.bind(this)
        this.refresh = this.refresh.bind(this)
    }

    componentDidMount () {
        // this.getAccount()
    }

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings
            }, () => {
                console.log(response)
            })
        })
    }

    handleHeaderColor (event) {
        const value = event.target.dataset.name
        const text = event.target.dataset.text

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                header_background_color: value,
                header_text_color: text
            }
        }), () => {
            this.setStorage()
        })
    }

    handleFooterColor (event) {
        const value = event.target.dataset.name
        const text = event.target.dataset.text

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                footer_background_color: value,
                footer_text_color: text
            }
        }), () => {
            this.setStorage()
        })
    }

    setStorage () {
        const device_settings = {
            footer_background_color: this.state.settings.footer_background_color || 'bg-dark',
            footer_text_color: this.state.settings.footer_text_color || 'bg-light',
            header_background_color: this.state.settings.header_background_color || 'bg-dark',
            header_text_color: this.state.settings.header_text_color || 'bg-light'
        }

        localStorage.setItem('device_settings', JSON.stringify(device_settings))
    }

    refresh () {
        axios.get('/api/accounts/refresh')
            .then((response) => {
                if (response.data.success === true) {
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

                    // save app state with user date in local storage
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

                    this.setState({ success_message: 'Refresh completed', success: true })
                }
            })
    }

    handleSettingsChange (event) {
        const name = event.target.name
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }), () => {
            switch (name) {
                case 'currency_format':
                    localStorage.setItem('currency_format', value)
                    break

                case 'dark_theme':
                    localStorage.setItem('dark_theme', value)
                    break

                case 'number_of_rows':
                    localStorage.setItem('number_of_rows', value)
                    break
            }
        })
    }

    getInventoryFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'dark_theme',
                    label: translations.dark_theme,
                    type: 'switch',
                    placeholder: translations.dark_theme,
                    value: settings.dark_theme
                },
                {
                    name: 'number_of_rows',
                    label: translations.number_of_rows,
                    type: 'select',
                    placeholder: translations.number_of_rows,
                    value: settings.number_of_rows,
                    options: [
                        {
                            value: 10,
                            text: 10
                        },
                        {
                            value: 25,
                            text: 25
                        },
                        {
                            value: 50,
                            text: 50
                        }
                    ]
                }
            ]
        ]

        return formFields
    }

    handleSubmit (e) {
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('first_month_of_year', this.state.first_month_of_year)
        formData.append('first_day_of_week', this.state.first_day_of_week)
        formData.append('_method', 'PUT')

        axios.post(`/api/accounts/${this.state.id}`, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({ success: true })
            })
            .catch((error) => {
                this.setState({ error: true })
            })
    }

    handleChange (event) {
        this.setState({ [event.target.name]: event.target.value })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const header_background_color = this.state.settings && this.state.settings.header_background_color ? this.state.settings.header_background_color : ''
        const footer_background_color = this.state.settings && this.state.settings.footer_background_color ? this.state.settings.footer_background_color : ''

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={this.state.settings_not_saved}/>

                <Header title={translations.device_settings}/>

                <div className="settings-container settings-container-narrow fixed-margin-extra">
                    <Card>
                        <CardBody>
                            <ColorPicker label={translations.header_background_color} value={header_background_color}
                                handleChange={this.handleHeaderColor}/>

                            <ColorPicker label={translations.footer_background_color} value={footer_background_color}
                                handleChange={this.handleFooterColor}/>

                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getInventoryFields()}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <Button onClick={this.refresh} color="primary" block>{translations.refresh}</Button>
                            <Button className="mt-2" onClick={(e) => {
                                e.preventDefault()
                                localStorage.removeItem('access_token')
                                window.location.href = '/#/login'
                            }} color="primary" block>{translations.logout}</Button>
                        </CardBody>
                    </Card>

                </div>
            </React.Fragment>
        )
    }
}
