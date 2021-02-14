import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'

class IntegrationSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            success_message: translations.settings_saved,
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            success: false,
            error: false,
            changesMade: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)

        this.model = new CompanyModel({ id: this.state.id })
    }

    componentDidMount () {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getAccount()
    }

    componentWillUnmount () {
        window.removeEventListener('beforeunload', this.beforeunload.bind(this))
    }

    beforeunload (e) {
        if (this.state.changesMade) {
            if (!confirm(translations.changes_made_warning)) {
                e.preventDefault()
                return false
            }
        }
    }

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings,
                cached_settings: response.settings
            }, () => {
                console.log(response)
            })
        })
    }

    handleChange (event) {
        this.setState({ [event.target.name]: event.target.value })
    }

    handleSettingsChange (event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        this.setState(prevState => ({
            changesMade: true,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleSubmit (e) {
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('_method', 'PUT')

        axios.post(`/api/accounts/${this.state.id}`, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({
                    success: true,
                    cached_settings: this.state.settings,
                    changesMade: false
                }, () => this.model.updateSettings(this.state.settings))
            })
            .catch((error) => {
                this.setState({ error: true })
            })
    }

    getFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'slack_webhook_url',
                    label: translations.slack_url,
                    type: 'text',
                    placeholder: translations.slack_url,
                    value: settings.slack_webhook_url,
                    help_url: 'https://my.slack.com/services/new/incoming-webhook/'
                },
                {
                    name: 'google_analytics_url',
                    label: translations.google_analytics_url,
                    type: 'text',
                    placeholder: translations.google_analytics_url,
                    value: settings.google_analytics_url
                }
            ]
        ]
    }

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={this.state.settings_not_saved}/>

                <Header title={translations.integration_settings} cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit}/>

                <div className="settings-container settings-container-narrow fixed-margin-extra">
                    <Card className="fixed-margin-extra">
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getFields()}
                            />
                        </CardBody>
                    </Card>
                </div>
            </React.Fragment>
        ) : null
    }
}

export default IntegrationSettings
