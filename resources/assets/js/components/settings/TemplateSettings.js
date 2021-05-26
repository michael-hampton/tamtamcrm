import React, {Component} from 'react'
import {Card, CardBody, Col, Form, Nav, NavItem, NavLink, Row, Spinner, TabContent, TabPane} from 'reactstrap'
import axios from 'axios'
import EmailFields from './EmailFields'
import EmailPreview from './EmailPreview'
import {translations} from '../utils/_translations'
import Variables from './Variables'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'
import Reminders from "./Reminders";

class TemplateSettings extends Component {
    constructor(props) {
        super(props)

        this.state = {
            success: false,
            error: false,
            showSpinner: false,
            showPreview: false,
            id: localStorage.getItem('account_id'),
            loaded: false,
            preview: null,
            activeTab: '1',
            template_type: 'email_template_invoice',
            template_name: 'Invoice',
            company_logo: null,
            cached_settings: {},
            changesMade: false,
            templates: []
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.toggle = this.toggle.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.getPreview = this.getPreview.bind(this)

        this.model = new CompanyModel({id: this.state.id})
    }

    componentDidMount() {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getTemplates()
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

    getTemplates() {
        const accountRepository = new AccountRepository()
        accountRepository.getTemplates().then(response => {
            if (!response) {
                alert('error')
                return false
            }

            this.setState({
                loaded: true,
                templates: response,
                cached_settings: response
            }, () => {
                console.log(response)
            })
        })
    }

    handleChange(event) {
        this.setState({[event.target.name]: event.target.value})

        if (event.target.name === 'template_type') {
            const name = event.target[event.target.selectedIndex].getAttribute('data-name')
            this.setState({template_name: name})
        }
    }

    handleSettingsChange(event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        const user_id = JSON.parse(localStorage.getItem('appState')).user.id

        const templates = {...this.state.templates}

        if (!templates[this.state.template_type]) {
            templates[this.state.template_type] = {
                template: this.state.template_name,
                account_id: parseInt(this.state.id),
                user_id: parseInt(user_id),
                enabled: true
            }
        }

        templates[this.state.template_type][name] = value

        this.setState({templates: templates})
    }

    handleFileChange(e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    toggle(tab) {
        if (this.state.activeTab !== tab) {
            this.setState({activeTab: tab}, () => {
                if (tab === '2') {
                    this.getPreview()
                }
            })
        }
    }

    getPreview() {
        this.setState({showSpinner: true, showPreview: false})

        const template = this.state.templates[this.state.template_type]

        const subject = template.subject
        const body = template.message

        axios.post('api/template', {
            subject: subject,
            body: body,
            template: this.state.template_type,
            entity_id: this.props.entity_id,
            entity: this.props.entity
        })
            .then((r) => {
                this.setState({
                    preview: r.data,
                    showSpinner: false,
                    showPreview: true
                })
            })
            .catch((e) => {
                this.setState({error: true})
            })
    }

    handleSubmitForReminder = () => {
        axios.post('/api/reminders', {reminders: this.state.reminders})
            .then((response) => {
                this.setState({
                    success: true,
                    changesMade: false
                })
            })
            .catch((error) => {
                console.error(error)
                // this.setState({
                //     errors: error.response.data.errors
                // })
            })
    }

    setReminders = (reminders) => {
        this.setState({reminders: reminders})
    }

    handleSubmit(e) {
        if (this.state.activeTab === '3') {
            return this.handleSubmitForReminder()
        }

        axios.post('/api/email_templates', {templates: this.state.templates})
            .then((response) => {
                this.setState({
                    success: true,
                    cached_settings: this.state.templates,
                    changesMade: false
                })
            })
            .catch((error) => {
                console.error(error)
                // this.setState({
                //     errors: error.response.data.errors
                // })
            })
    }

    handleCancel() {
        this.setState({settings: this.state.cached_settings, changesMade: false})
    }

    handleClose() {
        this.setState({success: false, error: false})
    }

    render() {
        const {templates, template_type} = this.state
        const fields = Object.keys(templates).length ? <EmailFields return_form={true} templates={templates}
                                                                    template_type={template_type}
                                                                    handleSettingsChange={this.handleSettingsChange}
                                                                    handleChange={this.handleChange}/> : null

        const preview = this.state.showPreview && this.state.preview && Object.keys(this.state.preview).length && this.state.templates[this.state.template_type] && Object.keys(this.state.templates[this.state.template_type]).length
            ? <EmailPreview preview={this.state.preview} entity={this.props.entity} entity_id={this.props.entity_id}
                            template_type={this.state.template_type}/> : null
        const spinner = this.state.showSpinner === true ? <Spinner style={{width: '3rem', height: '3rem'}}/> : null

        const tabs = <Nav tabs className="nav-justified setting-tabs disable-scrollbars">
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '1' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('1')
                    }}>
                    {translations.edit}
                </NavLink>
            </NavItem>
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('2')
                    }}>
                    {translations.preview}
                </NavLink>
            </NavItem>
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('3')
                    }}>
                    {translations.reminders}
                </NavLink>
            </NavItem>
        </Nav>

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                                 message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                                 message={translations.settings_not_saved}/>

                <Header title={translations.template_settings} cancelButtonDisabled={!this.state.changesMade}
                        handleCancel={this.handleCancel.bind(this)}
                        handleSubmit={this.handleSubmit}
                        tabs={tabs}/>

                <div className="settings-container settings-container-narrow fixed-margin-mobile">
                    <TabContent activeTab={this.state.activeTab}>
                        <TabPane tabId="1">
                            <Card>
                                <CardBody>
                                    <Row>
                                        <Col md={8}>
                                            <Form>
                                                {fields}
                                            </Form>
                                        </Col>

                                        <Col md={4}>
                                            <Variables class="fixed-margin-mobile"/>
                                        </Col>
                                    </Row>

                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="2">
                            <Card>
                                <CardBody>
                                    {spinner}
                                    {preview}
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="3">
                            <Card>
                                <CardBody>
                                    <Reminders reminders={this.state.reminders} setReminders={this.setReminders}/>
                                </CardBody>
                            </Card>
                        </TabPane>
                    </TabContent>
                </div>
            </React.Fragment>
        )
    }
}

export default TemplateSettings
