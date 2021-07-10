import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody, CardHeader, FormGroup, Label } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'
import BlockButton from '../common/BlockButton'
import { consts } from '../utils/_consts'
import CaseTemplateDropdown from '../common/dropdowns/CaseTemplateDropdown'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from '../common/EditScaffold'

export default class CaseSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            cached_settings: {},
            id: localStorage.getItem('account_id'),
            settings: {},
            activeTab: '1',
            success: false,
            error: false,
            changesMade: false,
            isSaving: false,
            loaded: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.toggle = this.toggle.bind(this)

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
        this.setState({ isSaving: true })
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
                    changesMade: false,
                    isSaving: false
                }, () => this.model.updateSettings(this.state.settings))
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    getCaseFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'default_case_priority',
                    label: translations.default_case_priority,
                    icon: `fa ${icons.envelope}`,
                    type: 'select',
                    options: [
                        {
                            value: consts.low_priority,
                            text: translations.low
                        },
                        {
                            value: consts.medium_priority,
                            text: translations.medium
                        },
                        {
                            value: consts.high_priority,
                            text: translations.high
                        }
                    ],
                    value: settings.default_case_priority,
                    group: 1
                },
                {
                    name: 'default_case_assignee',
                    label: translations.default_case_assignee,
                    icon: `fa ${icons.user}`,
                    type: 'user',
                    value: settings.default_case_assignee,
                    group: 1
                },
                {
                    name: 'case_forwarding_enabled',
                    label: translations.case_forwarding_enabled,
                    icon: `fa ${icons.image_file}`,
                    type: 'switch',
                    placeholder: translations.case_forwarding_enabled,
                    value: settings.case_forwarding_enabled
                },
                {
                    name: 'send_overdue_case_email',
                    label: translations.send_overdue_case_email,
                    icon: `fa ${icons.image_file}`,
                    type: 'switch',
                    placeholder: translations.send_overdue_case_email,
                    value: settings.send_overdue_case_email
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
        const tabs = {
            children: []
        }

        tabs.children[0] = <>
            <Card>
                <CardBody>
                    <FormBuilder
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getCaseFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardHeader>{translations.templates}</CardHeader>
                <CardBody>
                    <FormGroup>
                        <Label>{translations.new}</Label>
                        <CaseTemplateDropdown
                            template={this.state.settings.case_template_new}
                            name="case_template_new"
                            handleInputChanges={this.handleSettingsChange}
                        />
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.open}</Label>
                        <CaseTemplateDropdown
                            template={this.state.settings.case_template_open}
                            name="case_template_open"
                            handleInputChanges={this.handleSettingsChange}
                        />
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.closed}</Label>
                        <CaseTemplateDropdown
                            template={this.state.settings.case_template_closed}
                            name="case_template_closed"
                            handleInputChanges={this.handleSettingsChange}
                        />
                    </FormGroup>

                </CardBody>
            </Card>

            <BlockButton icon={icons.percent} button_text={translations.configure_categories}
                button_link="/#/case_categories"/>
        </>

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <EditScaffold isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                    isEditing={this.state.changesMade}
                    title={translations.case_settings}
                    cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit.bind(this)}
                    tabs={tabs}/>
            </React.Fragment>
        ) : null
    }
}
