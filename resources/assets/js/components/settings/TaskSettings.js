import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'

export default class TaskSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            settings: {},
            activeTab: '1',
            success: false,
            error: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    componentDidMount () {
        this.getAccount()
    }

    toggle (tab, e) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }

        const parent = e.currentTarget.parentNode
        const rect = parent.getBoundingClientRect()
        const rect2 = parent.nextSibling.getBoundingClientRect()
        const rect3 = parent.previousSibling.getBoundingClientRect()
        const winWidth = window.innerWidth || document.documentElement.clientWidth
        const widthScroll = winWidth * 33 / 100

        if (rect.left <= 10 || rect3.left <= 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft -= widthScroll
        }

        if (rect.right >= winWidth - 10 || rect2.right >= winWidth - 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft += widthScroll
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
                settings: response.settings
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
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value

        this.setState(prevState => ({
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
                this.setState({ success: true })
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    getTaskFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'task_rate',
                    label: translations.default_task_rate,
                    type: 'text',
                    value: settings.task_rate,
                    group: 1
                },
                {
                    name: 'task_automation_enabled',
                    label: translations.task_automation_enabled,
                    icon: `fa ${icons.envelope}`,
                    type: 'switch',
                    value: settings.task_automation_enabled,
                    help_text: translations.task_automation_enabled_help,
                    group: 1
                },
                {
                    name: 'include_task_documents',
                    label: translations.include_expense_documents,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.include_task_documents,
                    help_text: translations.include_expense_documents_help,
                    group: 1
                },
                {
                    name: 'show_tasks_onload',
                    label: translations.show_tasks_onload,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.show_tasks_onload,
                    help_text: translations.show_tasks_onload_help,
                    group: 1
                },
                {
                    name: 'include_times_on_invoice',
                    label: translations.include_times_on_invoice,
                    icon: `fa ${icons.archive}`,
                    type: 'switch',
                    value: settings.include_times_on_invoice,
                    help_text: translations.include_times_on_invoice_help,
                    group: 1
                }
            ]
        ]
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Header title={translations.task_settings} handleSubmit={this.handleSubmit}/>

                <div className="settings-container settings-container-narrow fixed-margin-extra">
                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getTaskFields()}
                            />
                        </CardBody>
                    </Card>
                </div>

            </React.Fragment>
        ) : null
    }
}
