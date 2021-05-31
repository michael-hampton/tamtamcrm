import React, {Component} from 'react'
import {Card, CardBody, FormGroup, Input, Label} from 'reactstrap'
import axios from 'axios'
import moment from 'moment'
import {translations} from '../utils/_translations'
import FormBuilder from './FormBuilder'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from "../common/EditScaffold";

export default class LocalisationSettings extends Component {
    constructor(props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            first_month_of_year: null,
            first_day_of_week: null,
            date_formats: ['DD/MMM/YYYY'],
            success: false,
            error: false,
            changesMade: false,
            isSaving: false,
            loaded: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.handleChange = this.handleChange.bind(this)

        this.model = new CompanyModel({id: this.state.id})
    }

    componentDidMount() {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getAccount()
    }

    componentWillUnmount() {
        window.removeEventListener('beforeunload', this.beforeunload.bind(this))
    }

    beforeunload(e) {
        if (this.state.changesMade) {
            if (!confirm(translations.changes_made_warning)) {
                e.preventDefault()
                return false
            }
        }
    }

    getAccount() {
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

    handleSettingsChange(event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        if (name === 'currency_format') {
            this.setState(prevState => ({
                changesMade: true,
                settings: {
                    ...prevState.settings,
                    show_currency_code: value === 'code'
                }
            }), () => {
                console.log('settings', this.state.settings)
            })

            return
        }

        this.setState(prevState => ({
            changesMade: true,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }), () => {
            console.log('settings', this.state.settings)
        })
    }

    getLanguageFields() {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'language_id',
                    label: translations.language,
                    type: 'language',
                    placeholder: translations.language,
                    value: settings.language_id,
                    group: 3
                }
            ]
        ]
    }

    getCurrencyFields() {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'currency_id',
                    label: translations.currency,
                    type: 'currency',
                    placeholder: translations.currency,
                    value: settings.currency_id,
                    group: 3
                },
                {
                    name: 'currency_format',
                    label: 'Currency Format',
                    type: 'select',
                    value: settings.show_currency_code === true ? 'code' : 'symbol',
                    options: [
                        {
                            value: 'code',
                            text: 'Code: 1000 GBP'
                        },
                        {
                            value: 'symbol',
                            text: 'Symbol: £1000'
                        }
                    ],
                    group: 1
                }
            ]
        ]
    }

    handleSubmit(e) {
        this.setState({isSaving: true})
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
                this.setState({
                    success: true,
                    cached_settings: this.state.settings,
                    changesMade: false,
                    isSaving: false
                })
                const appState = JSON.parse(localStorage.getItem('appState'))
                const account_id = appState.user.account_id
                const index = appState.accounts.findIndex(account => account.account_id === parseInt(account_id))
                appState.accounts[index].account.settings.language_id = this.state.language_id
                appState.accounts[index].account.settings.show_currency_code = this.state.currency_format === 'code'
                localStorage.setItem('appState', JSON.stringify(appState))
                console.log('user account', appState.accounts[index].account.settings.language_id)
            })
            .catch((error) => {
                this.setState({error: true})
            })
    }

    handleChange(event) {
        this.setState({[event.target.name]: event.target.value})
    }

    handleCancel() {
        this.setState({settings: this.state.cached_settings, changesMade: false})
    }

    handleClose() {
        this.setState({success: false, error: false})
    }

    render() {
        const {date_formats} = this.state
        const days = moment.weekdays()
        const months = moment.months()

        const month_list = months.map(function (item, i) {
            console.log('test')
            return <option key={i} value={item}>{item}</option>
        })

        const day_list = days.map(function (item, i) {
            console.log('test')
            return <option key={i} value={item}>{item}</option>
        })

        const date_format_list = date_formats && date_formats.length ? date_formats.map(date_format => {
            return <option key={date_format.id}
                           value={date_format.id}>{moment().format(date_format.format_moment)}</option>
        }) : null

        const tabs = {
            children: []
        }

        tabs.children[0] = <>
            <Card>
                <CardBody>
                    <FormGroup>
                        <Label>{translations.date_format}</Label>
                        <Input type="select" name="date_format" onChange={this.handleSettingsChange}>
                            {date_format_list}
                        </Input>
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.first_day_of_week}</Label>
                        <Input type="select" name="first_day_of_week" onChange={this.handleSettingsChange}>
                            <option value=""/>
                            {day_list}
                        </Input>
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.first_month_of_year}</Label>
                        <Input type="select" name="first_month_of_year" onChange={this.handleSettingsChange}>
                            <option value=""/>
                            {month_list}
                        </Input>
                    </FormGroup>

                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getLanguageFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getCurrencyFields()}
                    />
                </CardBody>
            </Card>
        </>

        return date_formats && date_formats.length ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                                 message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                                 message={translations.settings_saved}/>

                <EditScaffold isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                              isEditing={this.state.changesMade}
                              title={translations.localisation_settings}
                              cancelButtonDisabled={!this.state.changesMade}
                              handleCancel={this.handleCancel.bind(this)}
                              handleSubmit={this.handleSubmit.bind(this)}
                              tabs={tabs}/>
            </React.Fragment>
        ) : null
    }
}
